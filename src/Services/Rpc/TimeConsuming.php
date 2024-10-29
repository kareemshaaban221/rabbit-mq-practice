<?php

namespace App\Services\Rpc;

use App\Enums\RpcExpectedType;
use App\Services\Rpc\Abstracts\RpcService;

class TimeConsuming extends RpcService
{

    public static function sleep(int $seconds)
    {
        print("Sleeping {$seconds} seconds");
        foreach (range(1, $seconds) as $second) {
            print('.');
            sleep(1);
        }
        print("\n");
        static::setLastMethodReturnExceptedType(RpcExpectedType::VOID);
    }

}
