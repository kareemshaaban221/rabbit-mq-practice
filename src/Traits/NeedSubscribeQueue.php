<?php

namespace App\Traits;

use Closure;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Trait NeedSubscribeQueue
 * 
 * @property AMQPChannel $channel
 */
trait NeedSubscribeQueue
{
    public function getCallback(): Closure|array {
        return $this->callback ?? $this->getDefaultCallback();
    }

    public function setCallback(Closure|array $callback) {
        $this->callback = $callback;
    }

    public function declareConsumer($exchangeName = '')
    {
        foreach ($this->queues as $queueName) {
            $inputs = array_merge($this->consumerConfigs, [
                'queue' => $queueName,
                'consumer_tag' => $exchangeName,
                'callback' => $this->getCallback(),
            ]);
            $this->channel->basic_consume(...$inputs);
        }
    }

    public function run()
    {
        echo " [*] Waiting for messages From {$this->currentQueueName}. To exit press CTRL+C\n";
        try {
            $this->channel->consume();
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }
    }

    protected function getDefaultCallback() {
        return function (AMQPMessage $msg) {
            // '.' is the number of seconds this message take to be received
            // hack the sleep to make it more realistic as it's a heavy task
            sleep(substr_count($msg->getBody(), '.'));
            echo " [x] Received From {$msg->getRoutingKey()}: {$msg->getBody()}\n";
            // acknowledge the message
            $msg->ack();
        };
    }
}
