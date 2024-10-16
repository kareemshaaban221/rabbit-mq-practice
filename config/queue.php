<?php

return [
    'publish' => [
        'passive' => false,
        'durable' => false,
        'exclusive' => false,
        'auto_delete' => true
    ],
    'consume' => [
        'no_local' => false,
        'no_ack' => false, // false: message should be acknowledged by the consumer
        'exclusive' => false,
        'nowait' => false,
    ],
];
