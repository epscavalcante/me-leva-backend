<?php

namespace Core\Domain\Exceptions;

class RideCannotBeFinishedException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('Ride cannot be finished');
    }
}
