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

    public function publishMessage($messageBody, array $messageProperties = [])
    {
        if (empty($this->exchangeName)) {
            foreach ($this->queues as $queueName) {
                $this->publishMessageToQueue($queueName, $messageBody, $messageProperties);
            }
        } else {
            $this->publishMessageToExchange($this->exchangeName, $messageBody, $messageProperties);
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

    private function publishMessageToExchange($exchangeName, $messageBody, array $messageProperties = [])
    {
        $message = new AMQPMessage($messageBody, $messageProperties);
        $this->channel->basic_publish(
            $message,
            $exchangeName,
        );
        echo " [x] Sent To $exchangeName: '$messageBody'\n";
    }
}
