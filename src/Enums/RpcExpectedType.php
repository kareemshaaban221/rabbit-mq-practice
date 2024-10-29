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
}
