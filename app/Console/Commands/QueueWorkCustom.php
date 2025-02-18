<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



class QueueWorkCustom extends Command
{
    public $timeout = 600;

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
        set_time_limit(0); // PHP 스크립트 실행 제한 해제
        ini_set('memory_limit', '-1'); // 메모리 제한 해제


        // 제외할 큐 가져오기
        $exceptQueue = $this->option('except');

        // 현재 DB에 존재하는 큐 가져오기
        $queueList = DB::table('jobs')
            ->select('queue')
            ->distinct()
            ->pluck('queue')
            ->toArray();

        // 제외할 큐가 있다면 필터링
        if ($exceptQueue) {
            $queueList = array_filter($queueList, function ($queue) use ($exceptQueue) {
                return $queue !== $exceptQueue;
            });
        }

        // 실행할 큐 리스트 만들기
        $queueNames = implode(',', $queueList);

        if (empty($queueNames)) {
            Log::info("No queues available to process.");
            return;
        }

        Log::info("Starting queue worker with queues: " . $queueNames);

        // Laravel의 기본 queue:work 명령어 실행
        Artisan::call('queue:work', [
            '--queue' => $queueNames,
            '--tries' => 3,
            '--timeout' => $this->timeout,
            '--memory' => 1024,
        ]);
    }
}
