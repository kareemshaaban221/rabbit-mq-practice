<?php

namespace App\Interfaces;

use Closure;

interface ISubscriber
{

    public function getCallback(): Closure|array;
    public function setCallback(Closure|array $callback);
    public function declareConsumer($exchangeName = '');
    public function run();

}
