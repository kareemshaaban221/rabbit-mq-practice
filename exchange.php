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
$sender->declareExchange('chat', ExchangeType::TOPIC);
$sender->declareQueue('user1', true, 'user1.#');
$sender->declareQueue('user1', true, '#.group.#');
$sender->declareQueue('user2', true, 'user2.#');
$sender->declareQueue('user2', true, '#.group.#');
// $sender->declareQueue('console');
// $sender->declareQueue('console', true, '#.group.#');

// * update the routing key to make the message go to the correct queue
// * (*) is one word, (#) is anything
// [one] for console, [two] for user1, [two] for user2
$sender->publishMessage(json_encode($response), 'console.[anything]');
$sender->publishMessage(json_encode($response), 'user1.[anything]');
$sender->publishMessage(json_encode($response), 'user2.[anything]');
$sender->publishMessage(json_encode($response), '[anything].group.[anything]');
