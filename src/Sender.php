<?php

namespace App;

use Override;
use PhpAmqpLib\Message\AMQPMessage;

class Sender extends Entity
{
    #[Override]
    protected function setConfigs() {
        $this->configs = config('queue.publish');
    }

    public function publishMessage($messageBody, string $routingKey = '', array $messageProperties = [])
    {
        if (empty($this->exchangeName)) {
            foreach ($this->queues as $queueName) {
                $this->publishMessageToQueue($queueName, $messageBody, $messageProperties);
            }
        } else {
            $this->publishMessageToExchange($this->exchangeName, $messageBody, $routingKey, $messageProperties);
        }
    }

    private function publishMessageToQueue($queueName, $messageBody, array $messageProperties = [])
    {
        $message = new AMQPMessage($messageBody, $messageProperties);
        $this->channel->basic_publish(
            $message,
            '',
            $queueName
        );
        echo " [x] Sent To $queueName: '$messageBody'\n";
    }

    private function publishMessageToExchange($exchangeName, $messageBody, string $routingKey = '', array $messageProperties = [])
    {
        $message = new AMQPMessage($messageBody, $messageProperties);
        $this->channel->basic_publish(
            $message,
            $exchangeName,
            $routingKey,
        );
        echo " [x] Sent To $exchangeName Within [" . (empty($routingKey) ? 'Default Route' : $routingKey) . "]: '$messageBody'\n";
    }
}
