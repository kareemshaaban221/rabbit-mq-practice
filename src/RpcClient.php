<?php

namespace App;

use App\Enums\RpcExpectedType;
use App\Interfaces\IPublisher;
use App\Interfaces\ISubscriber;
use App\Resources\RpcResource;
use App\Traits\NeedPublishMessages;
use App\Traits\NeedSubscribeQueue;
use PhpAmqpLib\Message\AMQPMessage;

class RpcClient extends Entity implements IPublisher, ISubscriber
{
    use NeedPublishMessages,
        NeedSubscribeQueue;

    protected string $correlation_id;
    protected ?string $response;
    public static array $services;

    public function __construct()
    {
        parent::__construct();
        $this->setCallback([$this, 'onResponse']);
        $this->consumerConfigs['no_ack'] = true;
        $this->configs['durable'] = false;
        $this->configs['exclusive'] = true;
    }

    public function onResponse(AMQPMessage $msg)
    {
        // echo " [x] Received {$msg->get('correlation_id')}: {$msg->getBody()}\n";
        if ($msg->get('correlation_id') === $this->correlation_id) {
            $this->response = $msg->getBody();
        }
    }

    public function init()
    {
        $this->promptQueueName();
        $this->declareQueue($this->currentQueueName);
        $this->declareConsumer();
        echo "Will listen to queue: {$this->currentQueueName}\n";

        $this->response = null;
        $this->correlation_id = uniqid();

        $messageBody = json_encode([
            'init' => true,
        ]);
        $messageProperties = [
            'correlation_id'    => $this->correlation_id,
            'reply_to'          => $this->currentQueueName,
        ];

        $this->publishMessageToQueue(config('rpc.default_queue'), $messageBody, $messageProperties);

        echo "Waiting for response...\n";
        $this->wait();

        $response = json_decode($this->response ?? '', true);

        $services = $this->parseServices($response);
        $this->renderServices($services);
    }

    public function call($procedureName, $arguments = []): RpcResource
    {
        $this->promptQueueName();
        $this->declareQueue($this->currentQueueName);
        echo "Callback queue declared: {$this->currentQueueName}\n";

        $this->response = null;
        $this->correlation_id = uniqid();

        $messageBody = json_encode([
            'procedure' => $procedureName,
            'arguments' => $arguments,
        ]);
        $messageProperties = [
            'correlation_id'    => $this->correlation_id,
            'reply_to'          => $this->currentQueueName,
        ];

        $this->publishMessageToQueue(config('rpc.default_queue'), $messageBody, $messageProperties);

        echo "Waiting for response...\n";
        $this->wait();

        $response = json_decode($this->response ?? '', true);
        return new RpcResource(RpcExpectedType::find($response['type']), $response['result'], $response['exitCode']);
    }

    private function promptQueueName()
    {
        if (empty($this->currentQueueName)) {
            echo "Please specify a callback queue name.\n [Enter Queue Name] ";
            fscanf(STDIN, '%s', $queueName);
            $this->currentQueueName = $queueName;
        }
    }

    private function parseServices(array $services)
    {
        self::$services = [];
        foreach ($services as $service => $methods) {
            foreach ($methods as $method => $args) {
                if ($method == "getLastMethodReturnExceptedType") continue;
                $names = array_column($args, 'name');
                $types = array_column($args, 'type');
                self::$services[] = ["$service.$method", array_combine($names, $types)];
            }
        }
        return self::$services;
    }

    private function renderServices($services) {
        echo "\nAvailable services:\n";
        foreach ($services as $key => $service) {
            $argStr = '';
            foreach ($service[1] as $name => $type) {
                $argStr .= $type . ' ' . $name;
                if ($type != end($service[1])) {
                    $argStr .= ', ';
                }
            }
            echo "[$key] {$service[0]}($argStr)\n";
        }
    }

    private function wait()
    {
        while (!$this->response) {
            $this->channel->wait();
        }
    }

}
