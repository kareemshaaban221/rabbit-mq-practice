<?php

/**
 * Queue configuration file
 *
 * This file contains the configuration for the queue used by the RpcClient and
 * RpcServer classes. The configuration is used to define the settings for the
 * exchange, publish and consume operations.
 *
 * The configuration is an associative array with the following keys:
 *
 * - exchange: settings for the exchange declaration
 *   - passive: if true, the exchange is declared as passive
 *   - durable: if true, the exchange is declared as durable
 *   - auto_delete: if true, the exchange is deleted when the connection is closed
 *
 * - publish: settings for the publish operation
 *   - passive: if true, the exchange is declared as passive
 *   - durable: if true, the exchange is declared as durable
 *   - exclusive: if true, the exchange is declared as exclusive
 *   - auto_delete: if true, the exchange is deleted when the connection is closed
 *
 * - consume: settings for the consume operation
 *   - no_local: if true, the consumer will not consume messages published on the
 *               same connection
 *   - no_ack: if true, the consumer will not send an acknowledgement to the
 *             broker after consuming a message
 *   - exclusive: if true, the consumer will not allow other consumers to consume
 *                from the same queue
 *   - nowait: if true, the broker will not wait for a consumer to consume a
 *             message before sending the next message
 *
 * @package App\Config
 */

return [
    'exchange' => [
        'passive' => false,
        'durable' => true,
        'auto_delete' => false,
    ],
    'publish' => [
        'passive' => false,
        'durable' => true,
        'exclusive' => false,
        'auto_delete' => false
    ],
    'consume' => [
        'no_local' => false,
        'no_ack' => false, // false: message should be acknowledged by the consumer
        'exclusive' => false,
        'nowait' => false,
    ],
];
