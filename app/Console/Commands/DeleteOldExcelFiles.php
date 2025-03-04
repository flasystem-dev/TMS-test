<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class DeleteOldExcelFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orderExcel:delete-old-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete OrderExcel files older than 3 days from storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directory = storage_path('app/excel/order');
        $files = File::files($directory);

        foreach ($files as $file) {
            if (Carbon::createFromTimestamp($file->getMTime())->lt(Carbon::now()->subDays(3))) {
                File::delete($file->getRealPath());
//                $this->info("Deleted: " . $file->getFilename());
            }
        }

//        $this->info('Old Excel files cleanup complete.');
    }
}
