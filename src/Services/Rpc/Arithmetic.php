<?php

namespace App\Services\Rpc;

use App\Enums\RpcExpectedType;
use App\Services\Rpc\Abstracts\RpcService;

class Arithmetic extends RpcService
{

    public static function sum(int ...$numbers): int
    {
        static::setLastMethodReturnExceptedType(RpcExpectedType::NUMBER);
        return array_sum($numbers);
    }

    public static function factorial(int $n): int  
    {
        $result = 1;
        for ($i = 1; $i <= $n; $i++) {
            $result *= $i;
        }
        static::setLastMethodReturnExceptedType(RpcExpectedType::NUMBER);
        return $result;
    }
}
