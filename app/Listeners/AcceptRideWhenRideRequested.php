<?php

namespace App\Listeners;

use App\Account;
use App\Events\Ride\RideRequestedEvent;
use Core\Application\UseCases\AcceptRide;
use Core\Application\UseCases\DTOs\AcceptRideInput;

class AcceptRideWhenRideRequested
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly AcceptRide $acceptRide)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RideRequestedEvent $event): void
    {
        $availableDriver = Account::where('is_driver', true)->inRandomOrder()->firstOrCreate();
        if (! $availableDriver) {
            $availableDriver = Account::factory()->driver()->create();
        }

        $acceptRideInput = new AcceptRideInput(
            rideId: $event->getResourceId(),
            driverId: $availableDriver->account_id
        );
        $this->acceptRide->execute($acceptRideInput);
    }
}
