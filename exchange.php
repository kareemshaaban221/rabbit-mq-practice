<?php

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
$sender->declareQueue('user1');
$sender->declareQueue('user2');
$sender->declareQueue('console');

$sender->publishMessage(json_encode($response));
