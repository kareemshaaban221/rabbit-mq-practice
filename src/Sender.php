<?php

namespace App;

use App\Interfaces\IPublisher;
use App\Traits\NeedPublishMessages;

class Sender extends Entity implements IPublisher
{
    use NeedPublishMessages;
}
