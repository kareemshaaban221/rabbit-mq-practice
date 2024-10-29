<?php

use App\RpcServer;

// Load the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

$rpcServer  = new RpcServer;

$rpcServer->declareQueue(config('rpc.default_queue'));
$rpcServer->declareConsumer();
$rpcServer->run();
