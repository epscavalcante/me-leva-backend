<?php

namespace App\Repositories;

use App\Account as AccountModel;
use Core\Domain\Entities\Account;
use Core\Application\Repositories\AccountRepository;

class AccountModelRepository implements AccountRepository
{
    public function __construct(
        private readonly AccountModel $accountModel
    ) {}

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
        ]);
    }

    /**
     * @param  string $email
     * @return Account | null
     */
    public function getByEmail(string $email): object | null
    {
        $account = $this->accountModel->query()->where('email', $email)->first();
        if (!$account) return null;
        return new Account(
            accountId: $account->account_id,
            firstName: $account->first_name,
            lastName: $account->last_name,
            email: $account->email,
            phone: $account->phone,
            isDriver: $account->is_driver,
            isPassenger: $account->is_passenger,
        );
    }
}
