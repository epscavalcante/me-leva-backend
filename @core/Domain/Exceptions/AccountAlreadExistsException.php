<?php

namespace Core\Domain\Exceptions;

use Exception;

class AccountAlreadExistsException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('Account already exists');
    }
}
