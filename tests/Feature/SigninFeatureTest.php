<?php

use App\Account;

describe('Signin Feature Tests', function () {

    test('Receives 422 - empty data', function () {
        $response = $this->postJson(route('signin'));
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The email field is required. (and 1 more error)',
                'errors' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                ],
            ]);
    });

    test('Receives 422 - invalid email', function () {
        $response = $this->postJson(
            route('signin'),
            [
                'email' => 'john.doe',
                'password' => '12345678',
            ]
        );
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The email field must be a valid email address.',
                'errors' => [
                    'email' => ['The email field must be a valid email address.'],
                ],
            ]);
    });

    test('Receives 422 - invalid password', function () {
        $response = $this->postJson(
            route('signin'),
            [
                'email' => 'john.doe@email.com',
                'password' => '1234',
            ]
        );
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The password field must be at least 8 characters.',
                'errors' => [
                    'password' => ['The password field must be at least 8 characters.'],
                ],
            ]);
    });

    test('Receives 422 - invalid credentials - account not found', function () {
        $response = $this->postJson(
            route('signin'),
            [
                'email' => 'john.doe@email.com',
                'password' => '12345678',
            ]
        );
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    });

    test("Receives 422 - invalid credentials - account's password wrong", function () {
        $accountModel = Account::factory()->create(['password' => '12345678']);
        $response = $this->postJson(
            route('signin'),
            [
                'email' => $accountModel->email,
                'password' => 'password',
            ]
        );
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    });
});
