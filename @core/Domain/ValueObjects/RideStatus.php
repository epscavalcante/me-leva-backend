<?php

namespace Core\Domain\ValueObjects;

abstract class RideStatus
{
    public function __construct(
        private readonly string $value
    ) {}

    abstract public function request(): void;

    abstract public function accept(): void;

    abstract public function start(): void;

    abstract public function finish(): void;

    abstract public function cancel(): void;

    public function getValue(): string
    {
        return $this->value;
    }
}
