<?php

namespace Core\Domain\Exceptions;

class AccountCannotBeAcceptRideException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('Account cannot be accept ride');
    }
}
