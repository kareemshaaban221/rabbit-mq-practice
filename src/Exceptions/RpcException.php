<?php

namespace App\Exceptions;

/**
 * Class RpcException
 *
 * This class is used to represent an RPC related exception. It is used by the
 * RpcClient to throw an exception when an RPC call fails.
 *
 * The RpcException class implements the IRenderable interface, which means it
 * can be rendered as a string. The render method is used to return a string
 * representation of the exception, which is used by the RpcClient to display
 * the error message to the user.
 *
 * @package App\Exceptions
 */
class RpcException implements IRenderable
{

    /**
     * The error code of the exception.
     *
     * @var int
     */
    protected int $code;

    /**
     * Constructor for the RpcException class.
     *
     * @param int $code The error code of the exception.
     */
    public function __construct(
        int $code
    ) {
        $this->code = $code;
    }

    /**
     * Render the exception as a string.
     *
     * This method is used to return a string representation of the exception.
     * It is used by the RpcClient to display the error message to the user.
     *
     * @return string The string representation of the exception.
     */
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
