<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderExport;

use App\Models\Order\OrderExcelDownload;

class OrderExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderIdx;
    protected $fileName;
    protected $filePath;
    protected $downloadId;

    public function __construct($orderIdx, $fileName, $filePath, $downloadId)
    {
        $this->orderIdx = $orderIdx;
        $this->fileName = $fileName;
        $this->filePath = $filePath;
        $this->downloadId = $downloadId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            Excel::store(new OrderExport($this->orderIdx), $this->filePath, 'excel');

            OrderExcelDownload::where('id', $this->downloadId)->update([
                'status' => 'completed',
                'updated_at' => now()
            ]);

        }catch (\Exception $e) {
            \Log::error($e->getMessage());

            OrderExcelDownload::where('id', $this->downloadId)->update(['status' => 'failed','updated_at' => now()]);
        }
    }
}
