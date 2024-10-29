<?php

namespace App\Enums;

enum RpcExpectedType: string
{
    case NUMBER = 'number';
    case STRING = 'string';
    case BOOLEAN = 'boolean';
    case ARRAY = 'array';
    case OBJECT = 'object';
    case NULL = 'null';
    case VOID = 'void';

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
