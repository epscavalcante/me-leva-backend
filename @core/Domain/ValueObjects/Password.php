<?php

namespace Core\Domain\ValueObjects;

use Exception;

abstract class Password
{
    protected string $value;

    abstract public function getValue(): string;

    abstract public function getAlgorithm(): string;

    abstract public function check(string $password): bool;
}
