<?php

namespace App\Enums;

enum ExchangeType: string
{

    case FANOUT = "fanout";
    case DIRECT = "direct";
    case TOPIC  = "topic";
    case HEADER = "header";

}
