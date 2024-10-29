<?php

namespace App;

use App\Interfaces\IPublisher;
use App\Interfaces\ISubscriber;
use App\Traits\NeedPublishMessages;
use App\Traits\NeedSubscribeQueue;
use PhpAmqpLib\Message\AMQPMessage;

class RpcClient extends Entity implements IPublisher, ISubscriber
{
    use NeedPublishMessages,
        NeedSubscribeQueue;

    protected string $correlation_id;
    protected ?string $response;

    public function __construct()
    {
        parent::__construct();
        $this->setCallback([$this, 'onResponse']);
        $this->consumerConfigs['no_ack'] = true;
        $this->configs['durable'] = false;
        $this->configs['exclusive'] = true;
        $this->init();
    }

    public function onResponse(AMQPMessage $msg)
    {
        echo " [x] Received {$msg->get('correlation_id')}: {$msg->getBody()}\n";
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

        return json_decode($this->response ?? '', true);
    }

    public function call($procedureName, $arguments = [])
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

        return json_decode($this->response ?? '', true);
    }

    private function promptQueueName()
    {
        if (empty($this->currentQueueName)) {
            echo "Please specify a callback queue name.";
            fscanf(STDIN, '%s', $queueName);
            $this->currentQueueName = $queueName;
        }
    }

    private function wait()
    {
        while (!$this->response) {
            $this->channel->wait();
        }
    }

}
