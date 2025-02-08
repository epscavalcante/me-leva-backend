<?php

namespace Core\Domain\Exceptions;

class RideCannotBeStartedException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('Ride cannot be started');
    }
}
