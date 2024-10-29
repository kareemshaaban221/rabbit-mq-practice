<?php

namespace App\Interfaces;

interface IPublisher
{

    public function publishMessage($messageBody, string $routingKey = '', array $messageProperties = []);

}
