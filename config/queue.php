<?php

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
