<?php

/**
 * Receiver.php
 *
 * This file contains the Receiver class which is responsible for receiving
 * messages from a queue and performing the appropriate action based on the
 * message.
 *
 * @author  Kareem Mohamed <kareemshaaban221@gmail.com>
 */

namespace App;

use App\Interfaces\ISubscriber;
use App\Traits\NeedSubscribeQueue;

/**
 * Class Receiver
 *
 * The Receiver class is responsible for receiving messages from a queue and
 * performing the appropriate action based on the message.
 *
 * @category Messaging
 * @package  App
 */
class Receiver extends Entity implements ISubscriber
{
    /**
     * Uses the NeedSubscribeQueue trait to enable subscribing to a queue.
     *
     * @see \App\Traits\NeedSubscribeQueue
     */
    use NeedSubscribeQueue;
}
