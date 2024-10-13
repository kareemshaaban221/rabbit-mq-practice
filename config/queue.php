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
        'no_ack' => true,
        'exclusive' => false,
        'nowait' => false,
    ],
];
