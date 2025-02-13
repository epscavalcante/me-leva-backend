<?php

namespace Database\Factories;

use App\Ride;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Ride>
 */
class RideFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Ride::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ride_id' => $this->faker->uuid(),
            'passenger_id' => $this->faker->uuid(),
            'driver_id' => $this->faker->uuid(),
            'distance' => 0,
            'fare' => 0,
        ];
    }

    public function requested(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'requested',
        ]);
    }

    public function accepted($driverId): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
            'driver_id' => $driverId,
        ]);
    }

    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
