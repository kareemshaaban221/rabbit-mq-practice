<?php

namespace App;

use App\Enums\RpcExpectedType;
use App\Interfaces\IPublisher;
use App\Interfaces\ISubscriber;
use App\Resources\RpcResource;
use App\Services\Rpc\Abstracts\RpcService;
use App\Traits\NeedPublishMessages;
use App\Traits\NeedSubscribeQueue;
use PhpAmqpLib\Message\AMQPMessage;

class RpcServer extends Entity implements IPublisher, ISubscriber
{
    use NeedPublishMessages;
    use NeedSubscribeQueue {
        NeedSubscribeQueue::run as subscriberRun;
        NeedSubscribeQueue::declareConsumer as subscriberDeclareConsumer;
    }

    protected readonly array $services;
    protected string $service;

    public function __construct()
    {
        parent::__construct();
        $this->services = config('rpc.services');
        $this->setCallback(array($this, 'onResponse'));
    }

    public function onResponse(AMQPMessage $msg)
    {
        $request = json_decode($msg->getBody(), true);

        $queueName = $this->currentQueueName = $msg->get('reply_to');

        if (array_key_exists('init', $request)) {
            echo " [x] Init\n";
            $messageBody = json_encode($this->getServices());
        } else {
            echo " [.] Got {$request['procedure']}(" . implode(', ', $request['arguments']) . ")\n";
            $resource = $this->call($request['procedure'], $request['arguments']);
            $messageBody = $resource->toJson();
        }
        $messageProperties = ['correlation_id' => $msg->get('correlation_id')];

        $this->channel = $msg->getChannel();
        $this->publishMessageToQueue($queueName, $messageBody, $messageProperties);
        $msg->ack();
    }

    public function declareConsumer($exchangeName = '')
    {
        $this->channel->basic_qos(0, 1, false);
        $this->subscriberDeclareConsumer($exchangeName);
    }

    public function run()
    {
        echo " [x] Awaiting RPC requests\n";
        $this->subscriberRun();
    }

    private function call($procedureName, $arguments = []): RpcResource
    {
        [$class, $method] = explode('.', $procedureName);

        $type = RpcExpectedType::VOID;
        $result = null;
        $exitCode = 0;
        try {
            if (array_key_exists($class, $this->services)) {
                $this->service = $this->services[$class];
                if (method_exists($this->service, $method)) {
                    $reflection = new \ReflectionMethod($this->service, $method);
                    if ($reflection->getNumberOfParameters() === count($arguments)) {
                        $result = $this->service::$method(...$arguments);
                        $type = RpcService::getLastMethodReturnExceptedType();
                    } else {
                        echo "Error: Method $method requires " . $reflection->getNumberOfParameters() . " arguments, but " . count($arguments) . " were given";
                        $exitCode = 3;
                    }
                } else {
                    echo "Error: Method $method not found in class $class";
                    $exitCode = 2;
                }
            } else {
                echo "Error: Service $class not found";
                $exitCode = 1;
            }
        } catch (\Exception $e) {
            echo "Unkown Error: " . $e->getMessage();
            $exitCode = 4;
        } finally {
            return new RpcResource($type, $result, $exitCode);
        }
    }

    private function getServices(): array
    {
        $services = [];
        foreach ($this->services as $key => $service) {
            $methods = get_class_methods($service);
            $args = [];
            foreach ($methods as $method) {
                $reflection = new \ReflectionMethod($service, $method);
                $curr = [];
                foreach ($reflection->getParameters() as $parameter) {
                    $name = $parameter->name;
                    $type = (string) $parameter->getType();
                    $curr[] = compact('name', 'type');
                }
                $args[$method] = $curr;
            }
            $services[$key] = $args;
        }
        return $services;
    }
}
