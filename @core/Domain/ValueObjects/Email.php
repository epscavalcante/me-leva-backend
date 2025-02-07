<?php

namespace Core\Domain\ValueObjects;

use Exception;

class Email
{
    private string $email;

    public function __construct(
        string $email,
    ) {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid e-mail');
        }
        $this->email = $email;
    }

    public function getValue(): string
    {
        return $this->email;
    }
}
