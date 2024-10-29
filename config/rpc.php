<?php

use App\Services\Rpc\Arithmetic;
use App\Services\Rpc\TimeConsuming;

return [
    'default_queue' => 'rpc_queue',
    'namespace' => "App\\Services\\Rpc",

    'services' => [
        'arithmetic' => Arithmetic::class,
        'timeConsuming' => TimeConsuming::class,
    ]
];
