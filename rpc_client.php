<?php

/**
 * RpcClient.php
 *
 * This file contains the RpcClient class which is used to make RPC calls to
 * the server.
 *
 * @author  Kareem Mohamed <kareemshaaban221@gmail.com>
 */

use App\Exceptions\RpcException;
use App\Resources\RpcResource;
use App\RpcClient;

// Load the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Create an instance of the RpcClient class
$rpcClient  = new RpcClient;

// Run an infinite loop to keep the program running
while (true) {

    // Call the init method on the RpcClient instance
    $rpcClient->init();

    // Prompt the user to enter a number from the previous list
    echo "\nEnter a number previous list\n";

    // Prompt the user to enter a number
    echo "[Prompt] : ";

    // Get the user's input from the standard input
    fscanf(STDIN, '%s', $serviceNumber);

    // Check if the user's input is a valid service number
    if (array_key_exists($serviceNumber, RpcClient::$services)) {

        // Get the service details from the RpcClient::$services array
        $service = RpcClient::$services[$serviceNumber];

        // Get the service name and arguments
        $serviceName = $service[0];
        $serviceArgs = $service[1];

        // Initialize an empty array to store the user's inputs
        $inputs = [];

        // Loop through the service arguments and get the user's input for each one
        foreach ($serviceArgs as $name => $type) {

            // Prompt the user to enter a value for the argument
            echo "[Input] $name ($type): ";

            // Get the user's input from the standard input
            fscanf(STDIN, "%s", $input);

            // Convert the user's input to the correct type (array or string)
            $inputs[] = $type === 'array' ? json_decode($input, true) : $input;

        }

        // Call the call method on the RpcClient instance with the service name and inputs
        $response = $rpcClient->call($serviceName, $inputs);

        // Process the response from the server
        processResponse($response);

    } else {

        // Render an error message if the user's input is not a valid service number
        render(new RpcException(1));

    }

    // Prompt the user to press ENTER to continue or CTRL+C to exit
    echo "\nPress ENTER key to continue or CTRL+C to exit...\n";

    // Get the user's input from the standard input
    fscanf(STDIN, "%s", $key);

}

// Function to process the response from the server
function processResponse(RpcResource $resource) {

    // Check if the response has an error code
    if ($resource->exitCode !== 0) {

        // Render an error message if the response has an error code
        render(new RpcException($resource->exitCode));

    } else {

        // Print the result of the response if there is no error code
        echo "Result: {$resource->result}\n\n";

    }

}


