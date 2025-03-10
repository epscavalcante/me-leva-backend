<?php

namespace Core\Application\UseCases;

use App\Services\TokenGenerator\TokenGenerator;
use Core\Application\Repositories\AccountRepository;
use Core\Application\UseCases\DTOs\SigninInput;
use Core\Application\UseCases\DTOs\SigninOutput;
use Core\Domain\Exceptions\InvalidAccountCredentialsException;
use Core\Domain\Factories\PasswordFactory;

class Signin
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly TokenGenerator $tokenGenerator
    ) {
    }

    public function execute(SigninInput $input): SigninOutput
    {
        $account = $this->accountRepository->getByEmail($input->email);
        if (! $account) {
            // logar que a conta não encontrada
            throw new InvalidAccountCredentialsException();
        }

        $passwordHashed = PasswordFactory::create($input->password, $account->getPasswordAlgorithm());
        if (! $passwordHashed->check($account->getPassword())) {
            throw new InvalidAccountCredentialsException();
        }

        //gerar token
        // salvar tokens
        $tokenData = [
            'account_id' => $account->getId(),
            'name' => $account->getName(),
            'email' => $account->getEmail(),
            'is_passenger' => $account->isPassenger(),
            'is_driver' => $account->isDriver(),
        ];
        $token = $this->tokenGenerator->encode($tokenData);

        return new SigninOutput(
            accessToken: $token
        );
    }
}
