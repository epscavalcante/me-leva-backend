<?php

namespace App\Console\Commands;

use App\Account;
use App\Services\MessageBroker\MessageBroker;
use Core\Application\UseCases\AcceptRide;
use Core\Application\UseCases\DTOs\AcceptRideInput;
use Illuminate\Console\Command;

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
        $availableDriver = Account::where('is_driver', true)->inRandomOrder()->firstOrCreate();
        if (!$availableDriver) {
            $availableDriver = Account::factory()->driver()->create();
        }

        $messageBroker->consume(
            queue: $this->argument('queue'),
            callback: function ($msg) use ($acceptRide, $availableDriver) {
                $data = json_decode($msg->getBody(), true);
                $acceptRideInput = new AcceptRideInput(
                    rideId: $data['ride_id'],
                    driverId: $availableDriver->account_id
                );

                $acceptRide->execute($acceptRideInput);
            }
        );
    }
}
