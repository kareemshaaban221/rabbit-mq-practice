<?php

namespace App;

use Closure;
use Override;

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
        echo " [*] Waiting for messages. To exit press CTRL+C\n";
    }

    protected function getCallback(): Closure {
        return function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
        };
    }

    public function declareConsumer($exchangeName = '')
    {
        $inputs = array_merge($this->consumerConfigs, [
            'queue' => $this->queueName,
            'consumer_tag' => $exchangeName,
            'callback' => $this->getCallback(),
        ]);
        $this->channel->basic_consume(...$inputs);
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
