<?php

namespace App\Console\Commands;

use App\Services\MessageBroker\MessageBroker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

class RideUpdatedPositionConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consume:ride-updated-position';

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
                queue: 'ride_updated_position',
                callback: function ($data) {
                    Log::info('Process message', ['data' => $data]);
                    Broadcast::on("rides.{$data['ride_id']}")
                        ->as('RIDE.POSITION_UPDATED')
                        ->with($data)
                        ->sendNow();
                }
            );
            Log::info('End Consume message');
        }
    }
}
