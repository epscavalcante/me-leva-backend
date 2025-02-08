<?php

namespace Core\Domain\Exceptions;

class AccountCannotRequestRideException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('Account cannot request ride');
    }
}
