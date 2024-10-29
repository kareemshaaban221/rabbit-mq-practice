<?php

namespace App\Enums;

/**
 * Enum ExchangeType
 *
 * This enum represents the types of exchanges that can be declared in RabbitMQ.
 *
 * @package App\Enums
 */
enum ExchangeType: string
{

    /**
     * The fanout exchange type.
     *
     * This type of exchange routes all messages to all queues that are
     * bound to the exchange.
     */
    case FANOUT = "fanout";

    /**
     * The direct exchange type.
     *
     * This type of exchange routes messages to queues based on the routing
     * key.
     */
    case DIRECT = "direct";

    /**
     * The topic exchange type.
     *
     * This type of exchange routes messages to queues based on a pattern
     * in the routing key.
     */
    case TOPIC  = "topic";

    /**
     * The header exchange type.
     *
     * This type of exchange routes messages to queues based on the headers
     * of the message.
     */
    case HEADER = "header";

}
