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
            'distance' => $this->faker->numberBetween(),
            'fare' => $this->faker->numberBetween(),
            'from_latitude' => $this->faker->latitude(),
            'from_longitude' => $this->faker->longitude(),
            'to_latitude' => $this->faker->latitude(),
            'to_longitude' => $this->faker->longitude(),
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

    public function started(?string $driverId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
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
