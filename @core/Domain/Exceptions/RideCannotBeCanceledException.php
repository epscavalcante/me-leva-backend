<?php

namespace Core\Domain\Exceptions;

class RideCannotBeCanceledException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('Ride cannot be canceled');
    }
}
