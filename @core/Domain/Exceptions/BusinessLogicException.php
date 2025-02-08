<?php

namespace Core\Domain\Exceptions;

use Exception;

class BusinessLogicException extends Exception
{
    public function __construct(string $message = 'Invalid business logic')
    {
        parent::__construct($message);
    }
}
