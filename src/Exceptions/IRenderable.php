<?php

namespace App\Exceptions;

interface IRenderable
{
    public function render(): string;
}
