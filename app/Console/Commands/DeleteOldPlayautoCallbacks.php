<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeleteOldPlayautoCallbacks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'playauto:delete-old-callbacks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '플레이오토2 오래된 콜백 데이터 삭제';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oneMonthAgo = Carbon::now()->subMonth();
        DB::table('playauto2_callback')
            ->where('callback_time', '<', $oneMonthAgo)
            ->delete();
        $this->info('Old playauto callbacks deleted successfully.');
    }
}
