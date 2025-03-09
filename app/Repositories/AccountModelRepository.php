<?php

namespace App\Repositories;

use App\Account as AccountModel;
use Core\Application\Repositories\AccountRepository;
use Core\Domain\Entities\Account;

class AccountModelRepository implements AccountRepository
{
    public function __construct(
        private readonly AccountModel $accountModel
    ) {
    }

    public function save(object $account): void
    {
        $this->accountModel->create([
            'account_id' => $account->getId(),
            'first_name' => $account->getFirstName(),
            'last_name' => $account->getLastName(),
            'email' => $account->getEmail(),
            'phone' => $account->getPhone(),
            'is_driver' => $account->isDriver(),
            'is_passenger' => $account->isPassenger(),
            'password' => $account->getPassword(),
            'password_algorithm' => $account->getPasswordAlgorithm(),
        ]);
    }

    /**
     * @return Account | null
     */
    public function getById(string $accountId): ?object
    {
        $account = $this->getBy('account_id', $accountId);
        if (! $account) {
            return null;
        }

        return new Account(
            accountId: $account->account_id,
            firstName: $account->first_name,
            lastName: $account->last_name,
            email: $account->email,
            phone: $account->phone,
            isDriver: $account->is_driver,
            isPassenger: $account->is_passenger,
            password: $account->password,
            passwordAlgorithm: $account->password_algorithm,
        );
    }

    /**
     * @return Account[]
     */
    public function getByIds(array $accountIds): array
    {
        $accountModels = $this->accountModel->query()
            ->whereIn('account_id', $accountIds)
            ->get();
        if ($accountModels->count() === 0) {
            return [];
        }

        return array_map(
            callback: function (AccountModel $accountModel) {
                return new Account(
                    accountId: $accountModel->account_id,
                    firstName: $accountModel->first_name,
                    lastName: $accountModel->last_name,
                    email: $accountModel->email,
                    phone: $accountModel->phone,
                    isDriver: $accountModel->is_driver,
                    isPassenger: $accountModel->is_passenger,
                    password: $accountModel->password,
                    passwordAlgorithm: $accountModel->password_algorithm,
                );
            },
            array: $accountModels->all()
        );
    }

    /**
     * @return Account | null
     */
    public function getByEmail(string $email): ?object
    {
        $account = $this->getBy('email', $email);
        if (! $account) {
            return null;
        }
        return new Account(
            accountId: $account->account_id,
            firstName: $account->first_name,
            lastName: $account->last_name,
            email: $account->email,
            phone: $account->phone,
            isDriver: $account->is_driver,
            isPassenger: $account->is_passenger,
            password: $account->password,
            passwordAlgorithm: 'plain',//$account->password_algorithm,
        );
    }

    private function getBy(string $field, string|int $value): ?AccountModel
    {
        return $this->accountModel->query()
            ->firstWhere($field, $value);
    }
}
