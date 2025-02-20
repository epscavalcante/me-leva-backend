<?php

namespace Core\Application\Repositories;

use Core\Domain\Entities\Account;

interface AccountRepository
{
    /**
     * @param  Account  $entity
     */
    public function save(object $entity): void;

    /**
     * @return Account | null
     */
    public function getByEmail(string $email): ?object;

    /**
     * @return Account | null
     */
    public function getById(string $accountId): ?object;

    /**
     * @return Account[]
     */
    public function getByIds(array $accountId): array;
}
