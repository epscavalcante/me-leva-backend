<?php

use App\Account as AccountModel;
use App\Repositories\AccountModelRepository;
use Core\Application\UseCases\DTOs\GetAccountInput;
use Core\Application\UseCases\DTOs\GetAccountOutput;
use Core\Application\UseCases\DTOs\SignupInput;
use Core\Application\UseCases\DTOs\SignupOutput;
use Core\Application\UseCases\GetAccount;
use Core\Application\UseCases\Signup;
use Core\Domain\Exceptions\AccountAlreadExistsException;

describe('Signup', function () {

    test('Deve falhar ao criar uma conta com email existente', function () {
        $accountModel = AccountModel::factory()->passenger()->create();
        $accountRepository = new AccountModelRepository(accountModel: new AccountModel);
        $signup = new Signup(accountRepository: $accountRepository);
        $singupInput = new SignupInput('John', 'Doe', $accountModel->email, '00000000000', true, false, 'password');
        expect(fn () => $signup->execute($singupInput))->toThrow(AccountAlreadExistsException::class);
    });

    test('Deve criar um passageiro', function () {
        $accountRepository = new AccountModelRepository(accountModel: new AccountModel);
        $signup = new Signup(accountRepository: $accountRepository);
        $singupInput = new SignupInput('John', 'Doe', 'john.doe@email.com', '00000000000', true, false, 'password');
        $singupOutput = $signup->execute($singupInput);
        expect($singupOutput)->toBeInstanceOf(SignupOutput::class);
        expect($singupOutput->accountId)->toBeString();
        $getAccount = new GetAccount($accountRepository);
        $getAccountInput = new GetAccountInput($singupOutput->accountId);
        $getAccountOutput = $getAccount->execute($getAccountInput);
        expect($getAccountOutput)->toBeInstanceOf(GetAccountOutput::class);
        expect($getAccountOutput->accountId)->toBeString();
        expect($getAccountOutput->firstName)->toBe('John');
        expect($getAccountOutput->lastName)->toBe('Doe');
        expect($getAccountOutput->email)->toBe('john.doe@email.com');
        expect($getAccountOutput->phone)->toBe('00000000000');
        expect($getAccountOutput->isDriver)->toBe(false);
        expect($getAccountOutput->isPassenger)->toBe(true);
    });

    test('Deve criar um motorista', function () {
        $accountRepository = new AccountModelRepository(accountModel: new AccountModel);
        $signup = new Signup(accountRepository: $accountRepository);
        $singupInput = new SignupInput('John', 'Doe', 'john.doe@email.com', '00000000000', false, true, 'password');
        $singupOutput = $signup->execute($singupInput);
        expect($singupOutput)->toBeInstanceOf(SignupOutput::class);
        expect($singupOutput->accountId)->toBeString();
        $getAccount = new GetAccount($accountRepository);
        $getAccountInput = new GetAccountInput($singupOutput->accountId);
        $getAccountOutput = $getAccount->execute($getAccountInput);
        expect($getAccountOutput)->toBeInstanceOf(GetAccountOutput::class);
        expect($getAccountOutput->accountId)->toBeString();
        expect($getAccountOutput->firstName)->toBe('John');
        expect($getAccountOutput->lastName)->toBe('Doe');
        expect($getAccountOutput->email)->toBe('john.doe@email.com');
        expect($getAccountOutput->phone)->toBe('00000000000');
        expect($getAccountOutput->isDriver)->toBe(true);
        expect($getAccountOutput->isPassenger)->toBe(false);
    });
});
