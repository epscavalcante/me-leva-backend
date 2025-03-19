<?php

namespace App\Services\UnitOfWork;

interface UnitOfWork
{
    public function commit(): void;

    public function rollback(): void;
}
