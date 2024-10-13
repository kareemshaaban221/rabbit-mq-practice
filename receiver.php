<?php

use App\Receiver;

require_once __DIR__ . '/vendor/autoload.php';

$receiver = new Receiver();
$receiver->declareQueue('hello');
$receiver->declareConsumer();
$receiver->run();
