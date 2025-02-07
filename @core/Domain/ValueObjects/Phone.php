<?php

namespace Core\Domain\ValueObjects;

use Exception;

class Phone
{
    const PHONE_NUMBER_LENGTH = 11;

    private string $number;

    public function __construct(
        string $value,
    ) {
        if (strlen($value) !== self::PHONE_NUMBER_LENGTH) {
            throw new Exception('Invalid phone number');
        }
        $this->number = $value;
    }

    public function getValue(): string
    {
        return $this->number;
    }
}
