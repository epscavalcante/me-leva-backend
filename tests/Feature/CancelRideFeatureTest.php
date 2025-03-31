<?php

use App\Account;
use App\Ride;
use App\Services\TokenGenerator\TokenGenerator;
use Core\Domain\ValueObjects\Uuid;

describe('Cancel Ride Feature Tests', function () {

    test('Receives 401', function () {
        $response = $this->patchJson(route('rides.cancel', 'ride_id'));
        $response->assertForbidden();
    });

    test('Receives 404', function () {
        $account = Account::factory()->passenger()->create();
        $token = app(TokenGenerator::class)->encode($account->toArray());

        $response = $this->withToken($token)->patchJson(route('rides.cancel', 'ride_id'));
        $response->assertNotFound();
    });

    test('Receives 422', function () {
        $account = Account::factory()->passenger()->create();
        $ride = Ride::factory()->finished()->create(['driver_id' => (string) Uuid::create()]);
        $token = app(TokenGenerator::class)->encode($account->toArray());

        $response = $this->withToken($token)->patchJson(route('rides.cancel', $ride->ride_id));
        $response->assertUnprocessable();
    });

    test('Receives 200', function () {
        $ride = Ride::factory()->requested()->create();
        $account = Account::factory()->passenger()->create();
        $token = app(TokenGenerator::class)->encode($account->toArray());

        $response = $this->withToken($token)->patchJson(route('rides.cancel', $ride->ride_id));
        $response->assertNoContent();
    });
});
