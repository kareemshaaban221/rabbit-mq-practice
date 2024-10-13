<?php

namespace App;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;

abstract class Entity
{

    protected AbstractConnection $connection;
    protected AMQPChannel $channel;
    protected string $queueName;
    protected array $configs;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();
        $this->setConfigs();
    }

    protected abstract function setConfigs();

    public function declareQueue($queueName)
    {
        $this->channel->queue_declare($queueName, ...$this->configs);
        $this->queueName = $queueName;
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
