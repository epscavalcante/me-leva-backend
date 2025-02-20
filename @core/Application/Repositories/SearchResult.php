<?php

namespace Core\Application\Repositories;

interface SearchResult
{
    public function items(): array;

    public function total(): int;
}
