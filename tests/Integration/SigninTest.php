<?php

use App\Account as AccountModel;
use App\Repositories\AccountModelRepository;
use App\Services\TokenGenerator\MyJwt;
use Core\Application\UseCases\DTOs\SigninInput;
use Core\Application\UseCases\DTOs\SigninOutput;
use Core\Application\UseCases\Signin;
use Core\Domain\Exceptions\InvalidAccountCredentialsException;

describe('Signin', function () {

    test('Deve falhar ao não encontrar encontrar conta', function () {
        $accountRepository = new AccountModelRepository(accountModel: new AccountModel);
        $tokenGenerator = new MyJwt;
        $signin = new Signin(accountRepository: $accountRepository, tokenGenerator: $tokenGenerator);
        $singinInput = new SigninInput(email: 'john.doe@email.com', password: '12345678');
        expect(fn () => $signin->execute($singinInput))->toThrow(InvalidAccountCredentialsException::class);
    });

    test('Deve falhar ao logar com a senha incorreta', function () {
        $accountModel = AccountModel::factory()->create(['password' => 'password', 'password_algorithm' => 'plain']);
        $accountRepository = new AccountModelRepository(accountModel: new AccountModel);
        $tokenGenerator = new MyJwt;
        $signin = new Signin(accountRepository: $accountRepository, tokenGenerator: $tokenGenerator);
        $singinInput = new SigninInput(email: $accountModel->email, password: '12345678');
        expect(fn () => $signin->execute($singinInput))->toThrow(InvalidAccountCredentialsException::class);
    });

    test('Deve criar um token', function () {
        $accountModel = AccountModel::factory()->create(['password' => 'password', 'password_algorithm' => 'plain']);
        $accountRepository = new AccountModelRepository(accountModel: new AccountModel);
        $tokenGenerator = new MyJWT;
        $signin = new Signin(accountRepository: $accountRepository, tokenGenerator: $tokenGenerator);
        $singinInput = new SigninInput(email: $accountModel->email, password: 'password');
        $signinOutput = $signin->execute($singinInput);
        expect($signinOutput)->toBeInstanceOf(SigninOutput::class);
        expect($signinOutput->accessToken)->toBeString();
    });
});
