<?php

namespace App\Services\Rpc;

use App\Enums\RpcExpectedType;
use App\Services\Rpc\Abstracts\RpcService;

/**
 * Class TimeConsuming
 *
 * This class is a service that provides time-consuming operations to be
 * executed by the RPC server. It is used to test the performance and
 * concurrency of the RPC server.
 *
 * @package App\Services\Rpc
 */
class TimeConsuming extends RpcService
{

    /**
     * Sleeps for the given number of seconds.
     *
     * This method is used to test the performance and concurrency of the RPC
     * server. It is a time-consuming operation that blocks the execution of
     * other methods.
     *
     * @param int $seconds The number of seconds to sleep.
     *
     * @return void
     */
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
