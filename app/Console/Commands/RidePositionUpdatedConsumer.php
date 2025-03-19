<?php

namespace App\Console\Commands;

use App\Events\Ride\RidePositionUpdatedEvent;
use App\Services\MessageBroker\MessageBroker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RidePositionUpdatedConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consume:postion-updated';

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
                queue: 'ride_position_updated',
                callback: function ($data) {
                    Log::info('Process message', ['data' => $data]);
                    RidePositionUpdatedEvent::dispatch(
                        $data['ride_id'],
                        'RIDE.POSITION_UPDATED',
                        $data
                    );
                }
            );
            Log::info('End Consume message');
        }
    }
}
