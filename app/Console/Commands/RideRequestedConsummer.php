<?php

namespace App\Console\Commands;

use App\Account;
use App\Services\MessageBroker\MessageBroker;
use Core\Application\UseCases\AcceptRide;
use Core\Application\UseCases\DTOs\AcceptRideInput;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RideRequestedConsummer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consume:ride-requested';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(MessageBroker $messageBroker, AcceptRide $acceptRide)
    {
        while (true) {
            Log::info('Start Consume message');
            $messageBroker->consume(
                queue: 'ride_requested',
                callback: function ($data) use ($acceptRide) {
                    Log::info('Process message', ['data' => $data]);

                    sleep(3);

                    $availableDriver = Account::query()->where('is_driver', true)->inRandomOrder()->firstOrCreate();
                    if (! $availableDriver) {
                        $availableDriver = Account::factory()->driver()->create();
                    }

                    $acceptRideInput = new AcceptRideInput(
                        rideId: $data['ride_id'],
                        driverId: $availableDriver->account_id
                    );
                    $acceptRide->execute($acceptRideInput);
                }
            );
            Log::info('End Consume message');
        }
    }
}
