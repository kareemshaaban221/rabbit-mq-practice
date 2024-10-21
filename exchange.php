<?php

use App\Enums\ExchangeType;
use App\Sender;

require_once __DIR__ . '/vendor/autoload.php';

$message = $_REQUEST['message'] ?? '';
$username = $_REQUEST['username'] ?? '';

if (empty($message)) {
    echo "Please specify a message.";
    exit;
}

if (empty($username)) {
    echo "Please specify a username.";
    exit;
}

$response = [
    'message' => trim($message),
    'username' => trim($username),
];

$sender = new Sender();
$sender->declareExchange('chat', ExchangeType::DIRECT);
$sender->declareQueue('user1', true, 'group');
$sender->declareQueue('user2', true, 'group');
$sender->declareQueue('console', true, 'group');

$sender->publishMessage(json_encode($response), 'group');
