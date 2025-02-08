<?php

namespace Core\Domain\Exceptions;

class AccountNotFoundException extends NotFoundException
{
    public function __construct()
    {
        parent::__construct('Account not found');
    }
}
