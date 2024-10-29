<?php

namespace App\Exceptions;

class RpcException implements IRenderable
{

    public function __construct(
        protected int $code
    ) {}

    public function render(): string
    {
        return match ($this->code) {
            1 => "[RpcClientError] Service not found\n",
            2 => "[RpcClientError] Method not found\n",
            3 => "[RpcClientError] Method argument count error\n",
            default => "[RpcClientError] Unknown error\n",
        };
    }

}
