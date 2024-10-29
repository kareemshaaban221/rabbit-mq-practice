<?php

namespace App;

use App\Interfaces\ISubscriber;
use App\Traits\NeedSubscribeQueue;

class Receiver extends Entity implements ISubscriber
{
    use NeedSubscribeQueue;
}
