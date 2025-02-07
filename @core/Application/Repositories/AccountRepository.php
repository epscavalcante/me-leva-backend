<?php

namespace Core\Application\Repositories;

use Core\Domain\Account;

interface AccountRepository
{
    /**
     * @param  Account $entity
     */
    public function save(object $entity): void;

    /**
     * @param  string $email
     * @return Account | null
     */
    public function getByEmail(string $email): object | null;
}
