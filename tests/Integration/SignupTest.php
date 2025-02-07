<?php

use App\Account as AccountModel;
use App\Repositories\AccountModelRepository;
use Core\Application\UseCases\DTOs\SignupInput;
use Core\Application\UseCases\DTOs\SignupOutput;
use Core\Application\UseCases\Signup;
use Core\Domain\Exceptions\AccountAlreadExistsException;

describe('Signup', function () {

    test('Deve falhar ao criar uma conta com email existente', function () {
        $accountModel = AccountModel::factory()->passenger()->create();
        $accountRepository = new AccountModelRepository(accountModel: new AccountModel());
        $signup = new Signup(accountRepository: $accountRepository);
        $singupInput = new SignupInput('John', 'Doe', $accountModel->email, '00000000000', true, false);

        expect(fn() => $signup->execute($singupInput))->toThrow(AccountAlreadExistsException::class);
    });

    test('Deve criar um passageiro', function () {
        $accountRepository = new AccountModelRepository(accountModel: new AccountModel());
        $signup = new Signup(accountRepository: $accountRepository);
        $singupInput = new SignupInput('John', 'Doe', 'john.doe@email.com', '00000000000', true, false);
        $singupOutput = $signup->execute($singupInput);
        expect($singupOutput)->toBeInstanceOf(SignupOutput::class);
        expect($singupOutput->accountId)->toBeString();
    });

    test('Deve criar um motorista', function () {
        $accountRepository = new AccountModelRepository(accountModel: new AccountModel());
        $signup = new Signup(accountRepository: $accountRepository);
        $singupInput = new SignupInput('John', 'Doe', 'john.doe@email.com', '00000000000', false, true);
        $singupOutput = $signup->execute($singupInput);
        expect($singupOutput)->toBeInstanceOf(SignupOutput::class);
        expect($singupOutput->accountId)->toBeString();
    });
});
