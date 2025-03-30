<?php

namespace App\Console\Commands;

use App\Services\MessageBroker\MessageBroker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

class RideStartedConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consume:ride-started';

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
            Log::info('Process message from queue');
            $messageBroker->consume(
                queue: 'ride_started',
                callback: function (array $data) use ($messageBroker) {
                    Log::debug('Processing message', ['data' => $data]);

                    Broadcast::on("rides.{$data['ride_id']}")
                        ->as('RIDE.STARTED')
                        ->with([])
                        ->send();

                    sleep(rand(2, 5));

                    $positions = json_decode(
                        json: file_get_contents(database_path('mocks/ride-example/points_to_finish.json')),
                        associative: true
                    );

                    foreach ($positions as $position) {
                        $messageBroker->publish(
                            exchange: 'RIDE.UPDATED_POSITION',
                            data: [
                                'ride_id' => $data['ride_id'],
                                ...$position,
                            ]
                        );
                        sleep(rand(1, 3));
                    }
                }
            );
        }
    }
}
