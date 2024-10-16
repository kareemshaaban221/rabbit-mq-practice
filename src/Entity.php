<?php

namespace App;

use App\Enums\ExchangeType;
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
     * Name of the exchange
     *
     * @var string
     */
    protected string $exchangeName;

    /**
     * Type of the exchange
     *
     * @var ExchangeType
     */
    protected ExchangeType $exchangeType;

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
    public function declareQueue($queueName, bool $bindWithDeclaredExchange = false)
    {
        // Declare a queue with the given name and configuration
        $this->channel->queue_declare($queueName, ...$this->configs);

        if ($bindWithDeclaredExchange) {
            $this->channel->queue_bind($queueName, $this->exchangeName);
        }

        // Set the queue name
        $this->queues[] = $queueName;
    }

    /**
     * Declare an exchange
     *
     * @param string $exchangeName
     * @param ExchangeType $exchangeType
     *
     * @return void
     */
    public function declareExchange($exchangeName, ExchangeType $exchangeType)
    {
        // Declare an exchange with the given name and type
        $this->channel->exchange_declare($exchangeName, $exchangeType->value, ...config('queue.exchange'));
        
        // Set the exchange name
        $this->exchangeName = $exchangeName;

        // Set the exchange type
        $this->exchangeType = $exchangeType;
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
