<?php

use App\Enums\RpcExpectedType;
use App\RpcClient;

// Load the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

$rpcClient  = new RpcClient;

$args = [10];
$response = $rpcClient->call('arithmetic.factorial', $args);

if ($response['type'] == RpcExpectedType::NUMBER->value) {
    $rpName = 'factorial(' . implode(', ', $args) . ')';
    echo "Result of $rpName: {$response['result']}";
} else {
    echo "Error: Response cannot be parsed - expect a number";
    echo "Response: " . json_encode($response) . "";
}
