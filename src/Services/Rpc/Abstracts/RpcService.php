<?php

namespace App\Services\Rpc\Abstracts;

use App\Enums\RpcExpectedType;

abstract class RpcService
{
    private static RpcExpectedType $lastMethodReturnExceptedType;

    public static function getLastMethodReturnExceptedType(): RpcExpectedType
    {
        return static::$lastMethodReturnExceptedType;
    }

    protected static function setLastMethodReturnExceptedType(RpcExpectedType $type): void
    {
        static::$lastMethodReturnExceptedType = $type;
    }

}
