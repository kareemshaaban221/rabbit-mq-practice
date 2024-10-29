<?php

namespace App;

use App\Enums\ExchangeType;
use Closure;
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
     * Configuration for queue consumer
     * 
     * @var array
     */
    protected array $consumerConfigs;

    /**
     * Callback for consuming messages
     *
     * @var Closure|array
     */
    protected Closure|array $callback;

    /**
     * @var string
     */
    public string $currentQueueName;

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
        $this->setConsumerConfigs();
    }

    /**
     * Set configurations for queue declaration
     */
    protected function setConfigs() {
        $this->configs = config('queue.publish');
    }

    /**
     * Set configurations for queue consumer
     */
    protected function setConsumerConfigs() {
        $this->consumerConfigs = config('queue.consume');
    }

    /**
     * Declare a queue
     *
     * @param string $queueName
     *
     * @return void
     */
    public function declareQueue($queueName, bool $bindWithDeclaredExchange = false, string $bindingKey = '')
    {
        // Declare a queue with the given name and configuration
        $this->channel->queue_declare($queueName, ...$this->configs);

        if ($bindWithDeclaredExchange) {
            $this->channel->queue_bind($queueName, $this->exchangeName, $bindingKey);
        }

        // Set the queue name
        $this->currentQueueName = $queueName;
        $this->queues[]         = $queueName;
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
