<?php

use App\Account;
use App\Account as AccountModel;
use App\Repositories\AccountModelRepository;
use App\Repositories\RideModelRepository;
use App\Ride as RideModel;
use Core\Application\UseCases\DTOs\GetRidesInput;
use Core\Application\UseCases\DTOs\GetRidesOutput;
use Core\Application\UseCases\GetRides;

beforeEach(function () {
    $rideRepository = new RideModelRepository(new RideModel);
    $accountRepository = new AccountModelRepository(new AccountModel);
    $this->getRides = new GetRides(
        rideRepository: $rideRepository,
        accountRepository: $accountRepository
    );
});

describe('Get Rides', function () {

    test('Deve retornar uma lista vazia', function () {
        $input = new GetRidesInput;
        $output = $this->getRides->execute($input);
        expect($output)->toBeInstanceOf(GetRidesOutput::class);
    });

    test('Deve retornar uma lista com 5 itens', function () {
        RideModel::factory()
            ->requested()
            ->count(10)
            ->for(Account::factory()->passenger(), 'passenger')
            ->create();

        $input = new GetRidesInput(
            perPage: 5
        );
        $output = $this->getRides->execute($input);
        expect($output)->toBeInstanceOf(GetRidesOutput::class);
        expect($output->total)->toBe(10);
        expect($output->items)->toHaveCount(5);
    });

    test('Deve retornar uma lista com filtrado pelo status', function () {
        RideModel::factory()
            ->requested()
            ->count(4)
            ->for(Account::factory()->passenger(), 'passenger')
            ->create();

        $driver = Account::factory()->driver()->create();

        RideModel::factory()
            ->accepted($driver->account_id)
            ->count(2)
            ->for(Account::factory()->passenger(), 'passenger')
            ->create();

        $input = new GetRidesInput(
            status: 'accepted'
        );
        $output = $this->getRides->execute($input);
        expect($output)->toBeInstanceOf(GetRidesOutput::class);
        expect($output->total)->toBe(2);
        expect($output->items)->toHaveCount(2);
    });
});
