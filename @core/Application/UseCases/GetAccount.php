<?php

namespace Core\Application\UseCases;

use Core\Application\Repositories\AccountRepository;
use Core\Application\UseCases\DTOs\GetAccountInput;
use Core\Application\UseCases\DTOs\GetAccountOutput;
use Core\Domain\Exceptions\AccountNotFoundException;

class GetAccount
{
    public function __construct(
        private readonly AccountRepository $accountRepository
    ) {}

    public function execute(GetAccountInput $input): GetAccountOutput
    {
        $account = $this->accountRepository->getById($input->accountId);
        if (! $account) {
            throw new AccountNotFoundException;
        }

        return new GetAccountOutput(
            accountId: $account->getId(),
            firstName: $account->getFirstName(),
            lastName: $account->getLastName(),
            email: $account->getEmail(),
            phone: $account->getPhone(),
            isPassenger: $account->isPassenger(),
            isDriver: $account->isDriver()
        );
    }
}
