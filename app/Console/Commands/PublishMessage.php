<?php

namespace App\Console\Commands;

use App\Services\MessageBroker\MessageBroker;
use Illuminate\Console\Command;

class PublishMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:publish-message {exchange}';

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
        $data = [
            'data' => 'example',
        ];

        $messageBroker->publish(
            exchange: $this->argument('exchange'),
            data: $data
        );
    }
}
