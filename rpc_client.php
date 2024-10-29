<?php

use App\Exceptions\RpcException;
use App\Resources\RpcResource;
use App\RpcClient;

// Load the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

$rpcClient  = new RpcClient;

while (true) {
    $rpcClient->init();
    echo "\nEnter a number previous list\n";
    echo "[Prompt] : ";
    fscanf(STDIN, '%s', $serviceNumber);
    if (array_key_exists($serviceNumber, RpcClient::$services)) {
        $service = RpcClient::$services[$serviceNumber];
        $serviceName = $service[0];
        $serviceArgs = $service[1];
        $inputs = [];
        foreach ($serviceArgs as $name => $type) {
            echo "[Input] $name ($type): ";
            fscanf(STDIN, "%s", $input);
            $inputs[] = $type === 'array' ? json_decode($input, true) : $input;
        }
        $response = $rpcClient->call($serviceName, $inputs);
        processResponse($response);
    } else {
        render(new RpcException(1));
    }
    echo "\nPress ENTER key to continue or CTRL+C to exit...\n";
    fscanf(STDIN, "%s", $key);
}

function processResponse(RpcResource $resource) {

    if ($resource->exitCode !== 0) {
        render(new RpcException($resource->exitCode));
    } else {
        echo "Result: {$resource->result}\n\n";
    }

}
