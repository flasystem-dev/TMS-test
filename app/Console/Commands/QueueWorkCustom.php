<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Queue\Console\WorkCommand;
use Illuminate\Support\Facades\Log;

class QueueWorkCustom extends WorkCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:work-custom {--except=} {--queue=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Laravel queue worker with queue exclusion option';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $exceptQueue = $this->option('except');
        $queueList = explode(',', $this->option('queue'));

        if ($exceptQueue) {
            $queueList = array_filter($queueList, function ($queue) use ($exceptQueue) {
                return $queue !== $exceptQueue;
            });
        }

        $this->input->setOption('queue', implode(',', $queueList));

        Log::info("Queue Worker started with queues: " . implode(',', $queueList));

        parent::handle();
    }
}
