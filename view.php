<?php

// Use the Receiver class from the App namespace
use App\Receiver;

// Load the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Create a new Receiver object
$receiver = new Receiver();

// Declare a queue named 'hello'
$receiver->declareQueue('user1');
$receiver->declareQueue('user2');

// Declare a consumer on the queue
$receiver->declareConsumer();

// Start consuming messages from the queue
$receiver->run();

