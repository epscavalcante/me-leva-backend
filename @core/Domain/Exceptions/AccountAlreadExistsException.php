<?php

namespace Core\Domain\Exceptions;

class AccountAlreadExistsException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('Account already exists');
    }
}
