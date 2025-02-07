<?php

namespace Core\Domain\ValueObjects;

use Exception;
use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid
{
    public function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    public static function create()
    {
        return new self(RamseyUuid::uuid4()->toString());
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function validate()
    {
        if (! RamseyUuid::isValid($this->value)) {
            throw new Exception('Invalid uuid');
        }
    }

    public function __toString()
    {
        return $this->getValue();
    }
}
