<?php

/**
 * RpcClient.php
 *
 * This file contains the RpcClient class which is responsible for making RPC
 * calls to the server and listening for responses.
 *
 * @author  Kareem Mohamed <kareemshaaban221@gmail.com>
 * @license MIT
 * @link    https://github.com/kareem-shaaban/php-amqp-rpc
 */

namespace App;

use App\Enums\RpcExpectedType;
use App\Interfaces\IPublisher;
use App\Interfaces\ISubscriber;
use App\Resources\RpcResource;
use App\Traits\NeedPublishMessages;
use App\Traits\NeedSubscribeQueue;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class RpcClient
 *
 * The RpcClient class is responsible for making RPC calls to the server and
 * listening for responses.
 *
 * @category Rpc
 * @package  App
 */
class RpcClient extends Entity implements IPublisher, ISubscriber
{
    use NeedPublishMessages,
        NeedSubscribeQueue;

    /**
     * Correlation ID
     *
     * @var string
     */
    protected string $correlation_id;

    /**
     * Response from the server
     *
     * @var ?string
     */
    public ?string $response;

    /**
     * List of available services
     *
     * @var array
     */
    public static array $services;

    /**
     * RpcClient constructor
     *
     * @return void
     */
    public function __construct()
    {
        // Call the parent constructor to set up the basic settings
        parent::__construct();

        // Set the callback method that is called when a message is received from the queue
        $this->setCallback([$this, 'onResponse']);

        // Set the consumer configuration to not send an acknowledgement to the broker
        // after processing the message. This means that if the consumer crashes or
        // hangs, the message will be redelivered to the queue.
        $this->consumerConfigs['no_ack'] = true;

        // Set the queue configuration to be non-durable, meaning that the queue
        // will be deleted when the broker is restarted. We do this because we
        // want to make sure that the queue is always empty when we start the
        // client.
        $this->configs['durable'] = false;

        // Set the queue configuration to be exclusive, meaning that only one
        // consumer can consume from the queue at any given time. We do this
        // because we want to make sure that only one client is consuming from
        // the queue at any given time.
        $this->configs['exclusive'] = true;
    }

    /**
     * Callback method that is called when a message is received from the queue.
     *
     * @param AMQPMessage $msg The message received from the queue.
     *
     * @return void
     */
    public function onResponse(AMQPMessage $msg)
    {
        // Check if the message's correlation_id matches the one we sent
        if ($msg->get('correlation_id') === $this->correlation_id) {
            // If it does, store the message body in the $response variable
            $this->response = $msg->getBody();
        }
    }

    /**
     * Initialize the RpcClient.
     *
     * @return void
     */
    public function init()
    {
        // Prompt the user to enter a queue name
        $this->promptQueueName();

        // Declare a queue with the name entered by the user
        $this->declareQueue($this->currentQueueName);

        // Declare a consumer for the queue
        $this->declareConsumer();

        // Print a message indicating that the client will listen to the
        // specified queue
        echo "Will listen to queue: {$this->currentQueueName}\n";

        // Set the response to null and generate a unique correlation ID
        $this->response = null;
        $this->correlation_id = uniqid();

        // Encode an initialization request as a JSON string
        $messageBody = json_encode([
            'init' => true,
        ]);

        // Set the message properties for the initialization request
        $messageProperties = [
            'correlation_id'    => $this->correlation_id,
            'reply_to'          => $this->currentQueueName,
        ];

        // Publish the initialization request to the default queue
        $this->publishMessageToQueue(config('rpc.default_queue'), $messageBody, $messageProperties);

        // Print a message indicating that the client is waiting for a response
        echo "Waiting for response...\n";

        // Wait for the response from the server
        $this->wait();

        // Decode the response from the server as a JSON string
        $response = json_decode($this->response ?? '', true);

        // Parse the services from the response
        $services = $this->parseServices($response);

        // Render the list of services
        $this->renderServices($services);
    }

    /**
     * Call a procedure on the server.
     *
     * @param string $procedureName The name of the procedure to call.
     * @param array $arguments The arguments to pass to the procedure.
     *
     * @return RpcResource
     */
    public function call($procedureName, $arguments = []): RpcResource
    {
        // Prompt the user to enter a queue name if they haven't already done so
        $this->promptQueueName();

        // Declare a queue with the name entered by the user
        $this->declareQueue($this->currentQueueName);

        // Print a message indicating that the callback queue has been declared
        echo "Callback queue: {$this->currentQueueName}\n";

        // Set the response to null and generate a unique correlation ID
        $this->response = null;
        $this->correlation_id = uniqid();

        // Encode the procedure name and arguments as a JSON string
        $messageBody = json_encode([
            'procedure' => $procedureName,
            'arguments' => $arguments,
        ]);

        // Set the message properties for the RPC request
        $messageProperties = [
            'correlation_id'    => $this->correlation_id,
            'reply_to'          => $this->currentQueueName,
        ];

        // Publish the RPC request to the default queue
        $this->publishMessageToQueue(config('rpc.default_queue'), $messageBody, $messageProperties);

        // Print a message indicating that the client is waiting for a response
        echo "Waiting for response...\n";

        // Wait for the response from the server
        $this->wait();

        // Decode the response from the server as a JSON string
        $response = json_decode($this->response ?? '', true);

        // Create a new RpcResource object with the response
        return new RpcResource(
            RpcExpectedType::find($response['type']),
            $response['result'],
            $response['exitCode']
        );
    }

    /**
     * Prompt the user to enter a queue name.
     *
     * @return void
     */
    private function promptQueueName()
    {
        if (empty($this->currentQueueName)) {
            echo "Please specify a callback queue name.\n [Enter Queue Name] ";
            fscanf(STDIN, '%s', $queueName);
            $this->currentQueueName = $queueName;
        }
    }

    /**
     * Parse the services from the response.
     *
     * @param array $services The services from the response.
     *
     * @return array The parsed services.
     */
    private function parseServices(array $services)
    {
        self::$services = [];
        foreach ($services as $service => $methods) {
            foreach ($methods as $method => $args) {
                // Skip the method named "getLastMethodReturnExceptedType"
                if ($method == "getLastMethodReturnExceptedType") continue;
                
                // Extract parameter names and types from $args
                $names = array_column($args, 'name');
                $types = array_column($args, 'type');
                
                // Combine names and types into an associative array and add to services
                self::$services[] = ["$service.$method", array_combine($names, $types)];
            }
        }
        return self::$services;
    }

    /**
     * Render the services.
     *
     * @param array $services The services to render.
     *
     * @return void
     */
    private function renderServices($services)
    {
        echo "\nAvailable services:\n";
        foreach ($services as $key => $service) {
            // Initialize an empty string to store the arguments
            $argStr = '';
            // Loop through the arguments of the service
            foreach ($service[1] as $name => $type) {
                // Append the type and name of the argument to the string
                $argStr .= $type . ' ' . $name;
                // If it is not the last argument, append a comma and a space
                if ($type != end($service[1])) {
                    $argStr .= ', ';
                }
            }
            // Print the service name, arguments and key
            echo "[$key] {$service[0]}($argStr)\n";
        }
    }

    /**
     * Wait for the response from the server.
     *
     * @return void
     */
    private function wait()
    {
        // Wait for the response from the server
        // This is a blocking call, the program will wait here until a message is received
        while (!$this->response) {
            // Call the wait method on the channel
            // This will block until a message is received or the connection is closed
            $this->channel->wait();
        }
    }
}

