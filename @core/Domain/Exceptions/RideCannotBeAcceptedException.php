<?php

namespace Core\Domain\Exceptions;

class RideCannotBeAcceptedException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('Ride cannot be accepted');
    }
}
