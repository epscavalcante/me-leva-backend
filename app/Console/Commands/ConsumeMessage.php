<?php

namespace App\Console\Commands;

use App\Services\MessageBroker\MessageBroker;
use Illuminate\Console\Command;

class ConsumeMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:consume-message {queue}';

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
            callback: fn ($msg) => var_dump(json_decode($msg->getBody(), true))
        );
    }
}
