<?php

namespace Core\Domain\Events;

interface Event
{
    public static function name(): string;

    public function getEntityId(): string;

    public function getName(): string;

    public function getData(): array;
}
