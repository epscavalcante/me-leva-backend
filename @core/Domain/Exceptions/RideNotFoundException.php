<?php

namespace Core\Domain\Exceptions;

class RideNotFoundException extends NotFoundException
{
    public function __construct()
    {
        parent::__construct('Ride not found');
    }
}
