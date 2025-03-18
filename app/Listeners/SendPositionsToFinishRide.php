<?php

namespace App\Listeners;

use App\Events\Ride\RideStartedEvent;
use App\Services\MessageBroker\MessageBroker;

class SendPositionsToFinishRide
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly MessageBroker $messageBroker)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RideStartedEvent $event): void
    {
        $positions = json_decode(
            json: file_get_contents(database_path('mocks/ride-example/points_to_finish.json')),
            associative: true
        );

        foreach ($positions as $position) {
            $this->messageBroker->publish(
                exchange: $event->getEventName(),
                data: [
                    'ride_id' => $event->getResourceId(),
                    ...$position,
                ]
            );
            sleep(rand(1, 3));
        }
    }
}
