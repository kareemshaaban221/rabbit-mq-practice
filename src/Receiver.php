<?php

namespace App;

use App\Enums\ExchangeType;
use Closure;
use Override;
use PhpAmqpLib\Message\AMQPMessage;

class Receiver extends Entity
{
    protected array $consumerConfigs;
    protected Closure $callback;
    public string $currentQueueName;

    public function __construct()
    {
        parent::__construct();
        $this->setConsumerConfigs();
    }

    #[Override]
    public function declareQueue($queueName, bool $bindWithDeclaredExchange = false)
    {
        parent::declareQueue($queueName, $bindWithDeclaredExchange);
        echo " [*] Waiting for messages From $queueName. To exit press CTRL+C\n";
    }

    public function getCallback($queueName): Closure {
        return $this->callback ?? $this->getDefaultCallback($queueName);
    }

    public function setCallback(Closure $callback) {
        $this->callback = $callback;
    }

    public function declareConsumer($exchangeName = '')
    {
        foreach ($this->queues as $queueName) {
            $this->currentQueueName = $queueName;
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

    protected function setConsumerConfigs() {
        $this->consumerConfigs = config('queue.consume');
    }

    #[Override]
    protected function setConfigs() {
        $this->configs = config('queue.publish');
    }

    protected function getDefaultCallback($queueName) {
        return function (AMQPMessage $msg) use ($queueName) {
            // '.' is the number of seconds this message take to be received
            // hack the sleep to make it more realistic as it's a heavy task
            sleep(substr_count($msg->getBody(), '.'));
            echo " [x] Received From $queueName: {$msg->getBody()}\n";
            // acknowledge the message
            $msg->ack();
        };
    }

}
