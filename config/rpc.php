<?php

/**
 * RPC Configuration File
 *
 * This file contains the configuration for the RPC system, including the default
 * queue name, the namespace for the RPC services, and the list of available
 * services. Each service is associated with a key and references its class.
 *
 * @package App\Config
 */

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
