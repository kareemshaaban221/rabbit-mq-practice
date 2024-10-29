<?php

/**
 * NeedPublishMessages.php
 * 
 * This file contains the NeedPublishMessages trait which is used to publish
 * messages to RabbitMQ queues or exchanges.
 *
 * @author  Kareem Mohamed <kareemshaaban221@gmail.com>
 */

namespace App\Traits;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Trait NeedPublishMessages
 *
 * This trait is used to publish messages to RabbitMQ queues or exchanges.
 *
 * @category Messaging
 * @package  App
 *
 * @property AMQPChannel  $channel The channel to use when publishing messages.
 * @property array        $queues The queues to publish to.
 * @property string       $exchangeName The name of the exchange to publish to.
 */
trait NeedPublishMessages
{
    /**
     * Publish a message to a queue or exchange.
     *
     * If the exchange name is empty, the message is published to all queues
     * that are declared in the class. If the exchange name is not empty, the
     * message is published to the specified exchange.
     *
     * @param string $messageBody The body of the message to publish.
     * @param string $routingKey  The routing key to use when publishing to an
     *                            exchange.
     * @param array  $messageProperties The properties of the message to publish.
     *
     * @return void
     */
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

    /**
     * Publish a message to a queue.
     *
     * @param string $queueName The name of the queue to publish to.
     * @param string $messageBody The body of the message to publish.
     * @param array  $messageProperties The properties of the message to publish.
     *
     * @return void
     */
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

    /**
     * Publish a message to an exchange.
     *
     * @param string $exchangeName The name of the exchange to publish to.
     * @param string $messageBody The body of the message to publish.
     * @param string $routingKey The routing key to use when publishing to an
     *                           exchange.
     * @param array  $messageProperties The properties of the message to publish.
     *
     * @return void
     */
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

