<?php

namespace App\Interfaces;

/**
 * Interface IPublisher
 *
 * This interface defines the methods that any class that wants to publish
 * messages to RabbitMQ queues or exchanges must implement.
 *
 * @category Messaging
 * @package  App
 */
interface IPublisher
{

    /**
     * Publish a message to a queue or exchange.
     *
     * If the exchange name is empty, the message is published to all queues
     * that are declared in the class. If the exchange name is not empty, the
     * message is published to the specified exchange.
     *
     * @param string $messageBody The body of the message to publish.
     * @param string $routingKey The routing key to use when publishing to an
     *                           exchange.
     * @param array  $messageProperties The properties of the message to publish.
     *
     * @return void
     */
    public function publishMessage($messageBody, string $routingKey = '', array $messageProperties = []);

}
