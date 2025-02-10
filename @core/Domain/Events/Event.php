<?php

namespace Core\Domain\Events;

interface Event
{
    static function name(): string;

    public function getName(): string;

    public function getData(): array;
}
