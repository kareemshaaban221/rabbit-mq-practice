<?php

/**
 * Sender.php
 *
 * This file defines the Sender class, which is responsible for sending messages
 * using the publishing capabilities provided by the NeedPublishMessages trait.
 *
 * PHP version 8.0
 *
 * @category Messaging
 * @package  App
 * @author   Kareem Mohamed <kareemshaaban221@gmail.com>
 * @license  MIT License
 * @link     http://example.com
 */

namespace App;

use App\Interfaces\IPublisher;
use App\Traits\NeedPublishMessages;

/**
 * Class Sender
 *
 * The Sender class implements the IPublisher interface and provides
 * functionality for sending messages.
 *
 * @category Messaging
 * @package  App
 */
class Sender extends Entity implements IPublisher
{
    use NeedPublishMessages;

    // Class implementation goes here
}

