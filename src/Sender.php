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

    public function publishMessage($messageBody, $exchangeName = '')
    {
        $message = new AMQPMessage($messageBody);
        $this->channel->basic_publish(
            $message,
            $exchangeName,
            $this->queueName
        );
        echo " [x] Sent '$messageBody'\n";
    }
}
