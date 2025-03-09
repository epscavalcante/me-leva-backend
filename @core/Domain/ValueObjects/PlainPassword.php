<?php

namespace Core\Domain\ValueObjects;

use Exception;

class PlainPassword extends Password
{
    const ALGORITHM = 'plain';

    const MIN_LENGTH = 6;

    public function __construct(
        string $value,
    ) {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate($value)
    {
        $length = strlen($value);
        $isValid = $length > self::MIN_LENGTH;
        if (! $isValid) {
            throw new Exception('Invalid password');
        }
    }

    public function check(string $value): bool
    {
        return $this->value === $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getAlgorithm(): string
    {
        return self::ALGORITHM;
    }
}
