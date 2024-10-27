<?php

use App\Enums\ExchangeType;
// Use the Receiver class from the App namespace
use App\Receiver;

// Load the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Create a new Receiver object
$receiver = new Receiver();

// Declare a queue named 'hello'
// *
// we don't need to bind the queue with an exchange because it already done by the exchange.php file
// when the message sent from the user to the exchange
// *
$receiver->declareExchange('chat', ExchangeType::TOPIC);
$receiver->declareQueue('console', true, 'console.#');
// $receiver->declareQueue('console');

// Declare a consumer on the queue
$receiver->declareConsumer();

// Start consuming messages from the queue
$receiver->run();

