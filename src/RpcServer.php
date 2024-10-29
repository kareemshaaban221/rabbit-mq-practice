<?php

/**
 * RpcServer.php
 *
 * This file contains the RpcServer class which is used to set up an RPC server
 * that listens to a queue and calls the appropriate service based on the
 * procedure name and arguments in the message.
 *
 * @author  Kareem Mohamed <kareemshaaban221@gmail.com>
 */

namespace App;

use App\Enums\RpcExpectedType;
use App\Interfaces\IPublisher;
use App\Interfaces\ISubscriber;
use App\Resources\RpcResource;
use App\Services\Rpc\Abstracts\RpcService;
use App\Traits\NeedPublishMessages;
use App\Traits\NeedSubscribeQueue;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class RpcServer
 *
 * The RpcServer class is used to set up an RPC server that listens to a queue
 * and calls the appropriate service based on the procedure name and arguments
 * in the message.
 *
 * @category Rpc
 * @package  App
 */
class RpcServer extends Entity implements IPublisher, ISubscriber
{
    use NeedPublishMessages;
    use NeedSubscribeQueue {
        /**
         * Start the subscriber part of the RPC server.
         *
         * This method is used to start the subscriber part of the RPC server.
         * It is used to set up the subscriber and wait for messages.
         *
         * @return void
         */
        NeedSubscribeQueue::run as subscriberRun;

        /**
         * Declare a consumer for the RPC server.
         *
         * This method is used to declare a consumer for the RPC server.
         * It is used to set up the consumer and bind it to the appropriate
         * queue and exchange.
         *
         * @param string $exchangeName
         * @return void
         */
        NeedSubscribeQueue::declareConsumer as subscriberDeclareConsumer;
    }

    /**
     * Array of services that are available for RPC calls.
     *
     * This property is used to store the services that are available for RPC
     * calls. The services are stored in an associative array where the key
     * is the name of the service and the value is the class name of the
     * service.
     *
     * @var array
     */
    protected readonly array $services;

    /**
     * The current service being used.
     *
     * This property is used to store the current service being used.
     *
     * @var string
     */
    protected string $service;

    /**
     * Constructor for the RpcServer class.
     *
     * This constructor is used to set up the RpcServer class. It is used to
     * set up the services and the callback for the subscriber.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->services = config('rpc.services');
        $this->setCallback(array($this, 'onResponse'));
    }

    /**
     * Callback method that is called when a message is received from the queue.
     *
     * This method is used to process the message received from the queue. It
     * checks if the message contains an 'init' key and if it does, it returns
     * the list of available services. Otherwise, it calls the appropriate service
     * using the 'call' method and returns the result in a JSON format.
     *
     * @param  AMQPMessage  $msg  The message received from the queue.
     * @return void
     */
    public function onResponse(AMQPMessage $msg)
    {
        $request = json_decode($msg->getBody(), true);

        $queueName = $this->currentQueueName = $msg->get('reply_to');

        if (array_key_exists('init', $request)) {
            // If the message contains an 'init' key, it means it is an initialization
            // request. We return the list of available services.
            echo " [x] Init\n";
            $messageBody = json_encode($this->getServices());
        } else {
            // If the message does not contain an 'init' key, it means it is a
            // request to call a service. We call the appropriate service using
            // the 'call' method and return the result in a JSON format.
            echo " [.] Got {$request['procedure']}(" . implode(', ', $request['arguments']) . ")\n";
            $resource = $this->call($request['procedure'], $request['arguments']);
            $messageBody = $resource->toJson();
        }

        // We set the correlation_id to the message properties so that the client
        // can correlate the response with the request.
        $messageProperties = ['correlation_id' => $msg->get('correlation_id')];

        // We publish the message to the queue with the name of the client's
        // callback queue.
        $this->channel = $msg->getChannel();
        $this->publishMessageToQueue($queueName, $messageBody, $messageProperties);

        // We acknowledge the message so that the broker knows that we have
        // processed it.
        $msg->ack();
    }

    /**
     * Declare a consumer for the RPC server.
     *
     * Sets up a consumer by configuring the Quality of Service (QoS)
     * and binds it to the appropriate queue and exchange using the
     * subscriber's declare consumer method.
     *
     * @param string $exchangeName The name of the exchange to bind the consumer to.
     * @return void
     */
    public function declareConsumer($exchangeName = '')
    {
        // Set the QoS to process one message at a time
        $this->channel->basic_qos(0, 1, false);

        // Call the subscriber's method to declare a consumer
        $this->subscriberDeclareConsumer($exchangeName);
    }

    /**
     * Run the RPC server and listen for messages.
     *
     * This method is a wrapper around the subscriber's run method. It is
     * used to listen for messages in the queue and call the appropriate
     * service with the arguments from the message.
     *
     * @return void
     */
    public function run()
    {
        echo " [x] Awaiting RPC requests\n";
        $this->subscriberRun();
    }

    /**
     * Call the given service with the given arguments and return the result.
     *
     * This method is used to call the appropriate service with the arguments
     * from the message. It is called by the RpcServer's message handler.
     *
     * @param string $procedureName The name of the procedure to call (in the
     *                              format "ServiceName.MethodName").
     * @param array $arguments       The arguments to pass to the method.
     *
     * @return RpcResource The response from the server.
     */
    private function call($procedureName, $arguments = []): RpcResource
    {
        [$class, $method] = explode('.', $procedureName);

        $type = RpcExpectedType::VOID;
        $result = null;
        $exitCode = 0;
        try {
            // Check if the service class exists
            if (array_key_exists($class, $this->services)) {
                $this->service = $this->services[$class];
                // Check if the service method exists
                if (method_exists($this->service, $method)) {
                    // Get the method's reflection to get the number of parameters
                    $reflection = new \ReflectionMethod($this->service, $method);
                    // Check if the number of passed arguments matches the number of method parameters
                    if ($reflection->getNumberOfParameters() === count($arguments)) {
                        // Call the service method with the arguments
                        $result = $this->service::$method(...$arguments);
                        // Get the return type of the last method called
                        $type = RpcService::getLastMethodReturnExceptedType();
                    } else {
                        // Method parameter count mismatch error
                        echo "Error: Method $method requires " . $reflection->getNumberOfParameters() . " arguments, but " . count($arguments) . " were given";
                        $exitCode = 3;
                    }
                } else {
                    // Service method not found error
                    echo "Error: Method $method not found in class $class";
                    $exitCode = 2;
                }
            } else {
                // Service class not found error
                echo "Error: Service $class not found";
                $exitCode = 1;
            }
        } catch (\Exception $e) {
            // Unknown error
            echo "Unkown Error: " . $e->getMessage();
            $exitCode = 4;
        } finally {
            // Return the RpcResource with the result, exit code and the type of the result
            return new RpcResource($type, $result, $exitCode);
        }
    }

    /**
     * Returns an array of services and their methods and arguments.
     *
     * This method is used to get the list of available services and their
     * methods and arguments. The method iterates over the services array and
     * checks which methods are available in each service. For each method, it
     * gets the parameters and their types using reflection. The method returns
     * an array of services and their methods and arguments.
     *
     * @return array An array of services and their methods and arguments.
     */
    private function getServices(): array
    {
        // Iterate over the services array and get the methods for each service
        $services = [];
        foreach ($this->services as $key => $service) {
            $methods = get_class_methods($service);
            $args = [];
            // Iterate over the methods and get the parameters for each method
            foreach ($methods as $method) {
                $reflection = new \ReflectionMethod($service, $method);
                $curr = [];
                // Iterate over the parameters of the method and get their names and types
                foreach ($reflection->getParameters() as $parameter) {
                    $name = $parameter->name;
                    // Get the type of the parameter as a string
                    $type = (string) $parameter->getType();
                    // Create an array with the name and type of the parameter
                    $curr[] = compact('name', 'type');
                }
                // Add the parameters of the method to the $args array
                $args[$method] = $curr;
            }
            // Add the $args array to the $services array with the key of the service
            $services[$key] = $args;
        }
        // Return the $services array
        return $services;
    }

}
