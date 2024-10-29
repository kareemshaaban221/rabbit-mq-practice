<?php

namespace App\Interfaces;

use Closure;

/**
 * Interface ISubscriber
 *
 * This interface is used to define a subscriber that listens to messages in a
 * queue and calls the appropriate service based on the procedure name and
 * arguments in the message.
 *
 * @category Messaging
 * @package  App
 */
interface ISubscriber
{

    /**
     * Get the callback to use when consuming messages.
     *
     * @return Closure|array
     */
    public function getCallback(): Closure|array;

    /**
     * Set the callback to use when consuming messages.
     *
     * @param Closure|array $callback
     *
     * @return void
     */
    public function setCallback(Closure|array $callback);

    /**
     * Declare a consumer for the queue.
     *
     * @param string $exchangeName The name of the exchange to bind the queue to.
     *
     * @return void
     */
    public function declareConsumer($exchangeName = '');

    /**
     * Run the subscriber and listen for messages.
     *
     * @return void
     */
    public function run();

}
