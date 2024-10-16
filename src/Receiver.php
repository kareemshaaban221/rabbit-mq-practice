<?php

namespace App;

use Closure;
use Override;
use PhpAmqpLib\Message\AMQPMessage;

class Receiver extends Entity
{
    protected array $consumerConfigs;

    public function __construct()
    {
        parent::__construct();
        $this->setConsumerConfigs();
    }

    protected function setConsumerConfigs() {
        $this->consumerConfigs = config('queue.consume');
    }

    #[Override]
    protected function setConfigs() {
        $this->configs = config('queue.publish');
    }

    #[Override]
    public function declareQueue($queueName)
    {
        parent::declareQueue($queueName);
        echo " [*] Waiting for messages From $queueName. To exit press CTRL+C\n";
    }

    protected function getCallback($queueName): Closure {
        return function (AMQPMessage $msg) use ($queueName) {
            // '.' is the number of seconds this message take to be received
            // hack the sleep to make it more realistic as it's a heavy task
            sleep(substr_count($msg->getBody(), '.'));
            echo " [x] Received From $queueName: {$msg->getBody()}\n";
            // acknowledge the message
            $msg->ack();
        };
    }

    public function declareConsumer($exchangeName = '')
    {
        foreach ($this->queues as $queueName) {
            $inputs = array_merge($this->consumerConfigs, [
                'queue' => $queueName,
                'consumer_tag' => $exchangeName,
                'callback' => $this->getCallback($queueName),
            ]);
            $this->channel->basic_consume(...$inputs);
        }
    }

    public function run()
    {
        try {
            $this->channel->consume();
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }
    }

}
