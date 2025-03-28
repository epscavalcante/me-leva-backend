<?php

namespace App\Console\Commands;

use App\Events\Ride\RideRequestedEvent;
use App\Services\MessageBroker\MessageBroker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RideRequestedConsummer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consume:ride-requested {queue}';

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
            Log::info('Start Consume message');
            $messageBroker->consume(
                queue: $this->argument('queue'),
                callback: function ($data) {
                    Log::info('Process message', ['data' => $data]);

                    RideRequestedEvent::dispatch(
                        $data['ride_id'],
                        'RIDE.REQUESTED',
                        $data
                    );
                }
            );
            Log::info('End Consume message');
        }
    }
}
