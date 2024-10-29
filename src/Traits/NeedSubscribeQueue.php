<?php

namespace App\Traits;

use Closure;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Trait NeedSubscribeQueue
 * 
 * This trait is used to subscribe to a queue and listen for messages.
 *
 * @category Messaging
 * @package  App
 *
 * @property AMQPChannel $channel The channel to use when subscribing to a queue.
 * @property array  $queues The queues to subscribe to.
 * @property array  $consumerConfigs The configurations for the consumer.
 * @property string $currentQueueName The name of the current queue.
 * @property Closure|array $callback The callback to use when consuming messages.
 */
trait NeedSubscribeQueue
{
    /**
     * Get the callback to use when consuming messages.
     *
     * @return Closure|array
     */
    public function getCallback(): Closure|array {
        return $this->callback ?? $this->getDefaultCallback();
    }

    /**
     * Set the callback to use when consuming messages.
     *
     * @param Closure|array $callback
     *
     * @return void
     */
    public function setCallback(Closure|array $callback) {
        $this->callback = $callback;
    }

    /**
     * Declare a consumer for the queue.
     *
     * @param string $exchangeName The name of the exchange to bind the queue to.
     *
     * @return void
     */
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

    /**
     * Start consuming messages from the queue.
     *
     * @return void
     */
    public function run()
    {
        echo " [*] Waiting for messages From {$this->currentQueueName}. To exit press CTRL+C\n";
        try {
            $this->channel->consume();
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * Get the default callback to use when consuming messages.
     *
     * @return Closure
     */
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
