<?php

namespace App\Services\Rpc;

use App\Enums\RpcExpectedType;
use App\Services\Rpc\Abstracts\RpcService;

/**
 * Class Arithmetic
 *
 * This class provides a collection of arithmetic operations as RPC methods.
 *
 * @package App\Services\Rpc
 */
class Arithmetic extends RpcService
{

    /**
     * Calculates the sum of the given numbers.
     *
     * @param array $numbers
     * @return int
     */
    public static function sum(array $numbers): int
    {
        static::setLastMethodReturnExceptedType(RpcExpectedType::NUMBER);
        return array_sum($numbers);
    }

    /**
     * Calculates the factorial of the given number.
     *
     * @param int $n
     * @return int
     */
    public static function factorial(int $n): int  
    {
        $result = 1;
        for ($i = 1; $i <= $n; $i++) {
            $result *= $i;
        }
        static::setLastMethodReturnExceptedType(RpcExpectedType::NUMBER);
        return $result;
    }

    /**
     * Calculates the nth Fibonacci number.
     *
     * @param int $n
     * @return int
     */
    public static function fib(int $n): int  
    {
        static::setLastMethodReturnExceptedType(RpcExpectedType::NUMBER);
        if ($n <= 1) {
            return $n;
        }
        return self::fib($n - 1) + self::fib($n - 2);
    }
}

