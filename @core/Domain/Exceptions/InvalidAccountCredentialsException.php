<?php

namespace Core\Domain\Exceptions;

use Exception;

class InvalidAccountCredentialsException extends Exception
{
    public function __construct(string $message = 'Invalid credentials')
    {
        parent::__construct($message);
    }
}
