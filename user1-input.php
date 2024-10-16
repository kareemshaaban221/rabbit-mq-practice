<?php

use App\Sender;

require_once __DIR__ . '/vendor/autoload.php';

$sender = new Sender();
$sender->declareQueue('user1');

$message = $_REQUEST['message'] ?? '';
if (empty($message)) {
    echo "Please specify a message.";
    exit;
}

$sender->publishMessage(trim($message));
