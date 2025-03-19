<?php

namespace App\Console\Commands;

use App\Events\Ride\RideAcceptedEvent;
use App\Services\MessageBroker\MessageBroker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RideAcceptedPositionUpdatedConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consume:ride-accepted {queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(MessageBroker $messageBroker)
    {
        while (true) {
            Log::info("Process message from queue {$this->argument('queue')}");
            $messageBroker->consume(
                queue: $this->argument('queue'),
                callback: function (array $data) {
                    Log::debug('Processing message', ['data' => $data]);
                    RideAcceptedEvent::dispatch(
                        $data['ride_id'],
                        'RIDE.ACCEPTED',
                        [
                            'ride_id' => $data['ride_id'],
                            'driver_id' => 'motorista 1',
                            [
                                'position' => [
                                    'latitude' => -15.614348,
                                    'longitude' => -56.073746,
                                ],
                            ],
                        ]
                    );
                }
            );
        }

    }
}
