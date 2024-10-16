<?php

namespace App;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Abstract class for RabbitMQ entities
 */
abstract class Entity
{

    /**
     * Connection to RabbitMQ server
     *
     * @var AbstractConnection
     */
    protected AbstractConnection $connection;

    /**
     * AMQP channel
     *
     * @var AMQPChannel
     */
    protected AMQPChannel $channel;

    /**
     * Name of the queue
     *
     * @var array
     */
    protected array $queues = [];

    /**
     * Configuration for queue declaration
     *
     * @var array
     */
    protected array $configs;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Create a new connection to RabbitMQ server
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

        // Create a new channel
        $this->channel = $this->connection->channel();

        // Set configurations for queue declaration
        $this->setConfigs();
    }

    /**
     * Set configurations for queue declaration
     */
    protected abstract function setConfigs();

    /**
     * Declare a queue
     *
     * @param string $queueName
     *
     * @return void
     */
    public function declareQueue($queueName)
    {
        // Declare a queue with the given name and configuration
        $this->channel->queue_declare($queueName, ...$this->configs);

        // Set the queue name
        $this->queues[] = $queueName;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        // Close the channel
        $this->channel->close();

        // Close the connection
        $this->connection->close();
    }
}
