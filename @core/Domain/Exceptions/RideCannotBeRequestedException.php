<?php

namespace Core\Domain\Exceptions;

class RideCannotBeRequestedException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('Ride cannot be requested');
    }
}
