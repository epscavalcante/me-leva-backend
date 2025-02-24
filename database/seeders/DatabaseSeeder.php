<?php

namespace Database\Seeders;

use App\Account as AccountModel;
use App\Position as PositionModel;
use App\Ride as RideModel;
use Core\Domain\Entities\Position;
use Core\Domain\Services\DistanceCalculator;
use Core\Domain\ValueObjects\Uuid;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $passengers = AccountModel::factory(rand(2, 4))->passenger()->create();
        $drivers = AccountModel::factory(rand(2, 4))->driver()->create();

        $this->createRideRequested(
            $passengers->random(),
            [
                'latitude' => '-27.584905257808835',
                'longitude' => '-48.545022195325124',
            ],
            [
                'latitude' => '-27.496887588317275',
                'longitude' => '-48.522234807851476',
            ]
        );

        $this->createRideAccepted(
            $passengers->random(),
            $drivers->random(),
            [
                'latitude' => '-27.584905257808835',
                'longitude' => '-48.545022195325124',
            ],
            [
                'latitude' => '-27.496887588317275',
                'longitude' => '-48.522234807851476',
            ]
        );

        $ride1Positions = json_decode(file_get_contents(database_path('mocks/ride_1_positions.json')), true);
        $this->createRideCompleted($passengers->random(), $drivers->random(), $ride1Positions);

        $ride2Positions = json_decode(file_get_contents(database_path('mocks/ride_2_positions.json')), true);
        $this->createRideCompleted($passengers->random(), $drivers->random(), $ride2Positions);

        $ride3Positions = json_decode(file_get_contents(database_path('mocks/ride_3_positions.json')), true);
        $this->createRideCompleted($passengers->random(), $drivers->random(), $ride3Positions);
    }

    private function createRideRequested(AccountModel $passenger, array $from, array $to): RideModel
    {
        return RideModel::factory()->requested()->create([
            'passenger_id' => $passenger->account_id,
            'from_latitude' => (string) $from['latitude'],
            'from_longitude' => (string) $from['longitude'],
            'to_latitude' => (string) $to['latitude'],
            'to_longitude' => (string) $to['longitude'],
        ]);
    }

    private function createRideAccepted(AccountModel $passenger, AccountModel $driver, array $from, array $to): RideModel
    {
        return RideModel::factory()->accepted($driver->account_id)->create([
            'passenger_id' => $passenger->account_id,
            'from_latitude' => (string) $from['latitude'],
            'from_longitude' => (string) $from['longitude'],
            'to_latitude' => (string) $to['latitude'],
            'to_longitude' => (string) $to['longitude'],
        ]);
    }

    private function createRideCompleted(AccountModel $passenger, AccountModel $driver, array $positions): RideModel
    {
        $from = $positions[0];
        $to = $positions[count($positions) - 1];

        $rideId = Uuid::create();
        $distance = DistanceCalculator::calculateByPositions(
            positions: array_map(
                fn ($positionData) => Position::create($rideId, $positionData['latitude'], $positionData['longitude']),
                $positions
            )
        );
        $fare = $distance * 2.1;
        $ride = RideModel::factory()->finished()->create([
            'ride_id' => $rideId,
            'passenger_id' => $passenger->account_id,
            'driver_id' => $driver->account_id,
            'distance' => $distance,
            'fare' => $fare,
            'from_latitude' => (string) $from['latitude'],
            'from_longitude' => (string) $from['longitude'],
            'to_latitude' => (string) $to['latitude'],
            'to_longitude' => (string) $to['longitude'],
        ]);

        $positionsData = array_map(
            callback: function ($position) {
                $data = [
                    'latitude' => (string) $position['latitude'],
                    'longitude' => (string) $position['longitude'],
                ];
                $positionData = PositionModel::factory()->make($data);

                return $positionData->toArray();
            },
            array: $positions
        );
        $ride->positions()->createMany($positionsData);

        return $ride;
    }
}
