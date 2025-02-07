<?php

namespace Core\Domain\ValueObjects;

use Exception;

class Name
{
    const NAME_MAX_LENGTH = 50;

    const NAME_MIN_LENGTH = 2;

    private string $firstName;

    private string $lastName;

    public function __construct(
        string $firstName,
        string $lastName
    ) {
        $this->validate($firstName);
        $this->validate($lastName);
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    private function validate($value)
    {
        $length = strlen($value);
        $isValid = $length > self::NAME_MIN_LENGTH && $length <= self::NAME_MAX_LENGTH;
        if (!$isValid)
            throw new Exception('Invalid name');
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }
}
