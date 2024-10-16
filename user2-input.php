<?php

use App\Sender;

require_once __DIR__ . '/vendor/autoload.php';

$sender = new Sender();
$sender->declareQueue('user2');

echo "\033[32mWelcome to the PHP AMQP Sender [User 2].\033[0m\n";
echo "\033[33mPress Ctrl+C to exit.\033[0m\n";
while (true) {
    echo "Enter message: ";
    $message = fgets(STDIN);
    $sender->publishMessage(trim($message));
}
