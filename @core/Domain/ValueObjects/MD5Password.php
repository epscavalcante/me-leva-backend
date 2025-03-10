<?php

namespace Core\Domain\ValueObjects;

use Exception;

class MD5Password extends Password
{
    const ALGORITHM = 'md5';

    const MIN_LENGTH = 6;

    public function __construct(
        string $value,
    ) {
        $this->validate($value);
        $this->value = md5($value);
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
        return $this->value === md5($value);
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
