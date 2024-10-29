<?php

/**
 * RpcServer.php
 *
 * This file contains the RpcServer class which is used to set up an RPC server
 * that listens to a queue and calls the appropriate service based on the
 * procedure name and arguments in the message.
 *
 * @author  Kareem Mohamed <kareemshaaban221@gmail.com>
 */

use App\RpcServer;

// Load the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Create an instance of the RpcServer class
$rpcServer  = new RpcServer;

// Declare a queue with the name from the config file
$rpcServer->declareQueue(config('rpc.default_queue'));

// Declare a consumer for the queue
$rpcServer->declareConsumer();

// Start the RpcServer
$rpcServer->run();

