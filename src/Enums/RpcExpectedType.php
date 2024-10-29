<?php

namespace App\Enums;

/**
 * Enum RpcExpectedType
 *
 * This enum represents the expected return types for RPC calls.
 *
 * @package App\Enums
 */
enum RpcExpectedType: string
{
    case NUMBER = 'number';
    case STRING = 'string';
    case BOOLEAN = 'boolean';
    case ARRAY = 'array';
    case OBJECT = 'object';
    case NULL = 'null';
    case VOID = 'void';

    /**
     * Find the RpcExpectedType matching the given value.
     *
     * @param string $value The value to match.
     * @return RpcExpectedType|null The matching RpcExpectedType, or null if not found.
     */
    public static function find(string $value): ?RpcExpectedType
    {
        try {
            return static::from($value);
        } catch (\ValueError|\TypeError $e) {
            echo "[RpcServerError] Type [$value] is not supported type";
            exit(1);
        }
    }
}
