<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderDataBatchExport;

use App\Models\Order\OrderExcelDownload;

class OrderExportJob implements ShouldQueue, ShouldBeUnique
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

            Excel::store(new OrderDataBatchExport($this->orderIdx, $this->downloadId), $this->filePath, 'excel');

            OrderExcelDownload::where('id', $this->downloadId)->update([
                'status' => 'completed',
                'progress' => 100,
                'completed_time' => now()
            ]);

        }catch (\Exception $e) {
            \Log::error($e->getMessage());

            OrderExcelDownload::where('id', $this->downloadId)->update(['status' => 'failed','updated_at' => now()]);
        }
    }

    public function uniqueId()
    {
        return 'order_export_job';
    }
}
