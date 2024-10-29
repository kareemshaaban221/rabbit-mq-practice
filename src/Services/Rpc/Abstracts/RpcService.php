<?php

namespace App\Services\Rpc\Abstracts;

use App\Enums\RpcExpectedType;

/**
 * Abstract class for RPC services.
 *
 * This class is used to store the expected return type of the last method
 * called in an RPC service. The expected return type is used by the RpcServer
 * to determine the type of the result returned by the service.
 *
 * @author  Kareem Mohamed <kareemshaaban221@gmail.com>
 */
abstract class RpcService
{
    /**
     * The expected return type of the last method called in the service.
     *
     * @var RpcExpectedType
     */
    private static RpcExpectedType $lastMethodReturnExceptedType;

    /**
     * Get the expected return type of the last method called in the service.
     *
     * @return RpcExpectedType The expected return type.
     */
    public static function getLastMethodReturnExceptedType(): RpcExpectedType
    {
        return static::$lastMethodReturnExceptedType;
    }

    /**
     * Set the expected return type of the last method called in the service.
     *
     * @param RpcExpectedType $type The expected return type.
     *
     * @return void
     */
    protected static function setLastMethodReturnExceptedType(RpcExpectedType $type): void
    {
        static::$lastMethodReturnExceptedType = $type;
    }

}

