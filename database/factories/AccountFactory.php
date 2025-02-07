<?php

namespace Database\Factories;

use App\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Account>
 */
class AccountFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => $this->faker->uuid(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->email(),
            'phone' => $this->faker->numerify("###########"),
            'is_passenger' => $this->faker->boolean(),
            'is_driver' => $this->faker->boolean(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function driver(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_driver' => true,
            'is_passenger' => false
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function passenger(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_driver' => false,
            'is_passenger' => true
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function driverAndPassenger(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_driver' => true,
            'is_passenger' => true
        ]);
    }
}
