<?php

use App\Sender;

require_once __DIR__ . '/vendor/autoload.php';

$sender = new Sender();
$sender->declareQueue('hello');
$sender->publishMessage($argv[1] ?? 'Hello World!');
