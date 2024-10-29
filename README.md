# RPC Client-Server using RabbitMQ

This project is a simple implementation of an RPC (Remote Procedure Call) client-server architecture using RabbitMQ. It demonstrates how to execute remote procedures and handle responses asynchronously over RabbitMQ.

## Prerequisites

- PHP 7.4 or later
- Composer
- RabbitMQ server

## Installation

1. Clone the repository:

    ```sh
    git clone <repository-url>
    cd <repository-directory>
    ```

2. Install the dependencies using Composer:

    ```sh
    composer install
    ```

3. Configure RabbitMQ by setting up the appropriate queues and exchanges as defined in the `config` directory.

## Usage

### Running the Server

To start the RPC server, run the following command:

```sh
php rpc_server.php
```

The server will listen for incoming RPC requests and handle them according to the available services.

### Running the Client

To make an RPC call from the client, run the following command:

```sh
php rpc_client.php
```

The client will send a request to the server and display the result once it receives a response.

## Configuration

The configuration files are located in the `config` directory. They allow you to set up:

- Default queue names
- Namespace for services
- List of available services

Modify these files to suit your environment and requirements.

## Services

The project includes example services located in the `src/Services/Rpc` directory:

- `Arithmetic`: Provides arithmetic operations such as sum and factorial.
- `TimeConsuming`: Provides operations that simulate time-consuming tasks.

## Extending the Project

To add new services, create a new class in the `src/Services/Rpc` directory and update the `config/rpc.php` file to include the new service.

