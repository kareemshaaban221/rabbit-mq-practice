<?php

namespace App\Services\Rpc;

use App\Enums\RpcExpectedType;
use App\Services\Rpc\Abstracts\RpcService;

class Arithmetic extends RpcService
{

    public static function sum(array $numbers): int
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

    public static function fib(int $n): int  
    {
        static::setLastMethodReturnExceptedType(RpcExpectedType::NUMBER);
        if ($n <= 1) {
            return $n;
        }
        return self::fib($n - 1) + self::fib($n - 2);
    }
}
