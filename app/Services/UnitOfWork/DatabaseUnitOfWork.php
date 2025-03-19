<?php

namespace App\Services\UnitOfWork;

use Illuminate\Support\Facades\DB;

class DatabaseUnitOfWork implements UnitOfWork
{
    public function __construct()
    {
        DB::beginTransaction();
    }

    public function commit(): void
    {
        DB::commit();
    }

    public function rollback(): void
    {
        DB::rollBack();
    }
}
