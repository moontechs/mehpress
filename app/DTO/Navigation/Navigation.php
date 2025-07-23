<?php

namespace App\DTO\Navigation;

class Navigation
{
    public function __construct(public readonly ?Element $previous, public readonly ?Element $next) {}
}
