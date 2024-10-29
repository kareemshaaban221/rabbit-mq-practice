<?php

namespace App\Exceptions;

/**
 * Interface IRenderable
 *
 * This interface is used to define a common method for rendering an exception
 * as a string. It is used by the RpcClient to display the error message to the
 * user.
 *
 * @package App
 */
interface IRenderable
{
    /**
     * Render the exception as a string.
     *
     * This method is used to return a string representation of the exception.
     * It is used by the RpcClient to display the error message to the user.
     *
     * @return string The string representation of the exception.
     */
    public function render(): string;
}

