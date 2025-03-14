<?php

namespace Core\Application\UseCases;

use Core\Application\Repositories\AccountRepository;
use Core\Application\UseCases\DTOs\SignupInput;
use Core\Application\UseCases\DTOs\SignupOutput;
use Core\Domain\Entities\Account;
use Core\Domain\Exceptions\AccountAlreadExistsException;

class Signup
{
    public function __construct(
        private readonly AccountRepository $accountRepository
    ) {}

    public function execute(SignupInput $input): SignupOutput
    {
        $account = Account::create(
            firstName: $input->firstName,
            lastName: $input->lastName,
            email: $input->email,
            phone: $input->phone,
            isDriver: $input->isDriver,
            isPassenger: $input->isPassenger,
            password: $input->password
        );

        $accountExists = $this->accountRepository->getByEmail($account->getEmail());

        if ($accountExists) {
            throw new AccountAlreadExistsException;
        }

        $this->accountRepository->save($account);

        // send Email

        return new SignupOutput($account->getId());
    }
}
