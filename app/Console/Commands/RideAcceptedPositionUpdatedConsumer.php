<?php

namespace App\Console\Commands;

use App\Events\Ride\RidePositionUpdatedEvent;
use App\Services\MessageBroker\MessageBroker;
use Illuminate\Console\Command;

class RideAcceptedPositionUpdatedConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consume:ride-accepted-position-updated {queue}';

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
        $messageBroker->consume(
            queue: $this->argument('queue'),
            callback: function ($msg) {
                $data = json_decode($msg->getBody(), true);
                RidePositionUpdatedEvent::dispatch($data['ride_id'], 'RIDE.POSITION_UPDATED', $data);
            }
        );
    }
}
