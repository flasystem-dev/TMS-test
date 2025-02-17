<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithUpsertColumns;
use App\Models\TMS_Product;

class ProductSampleImport implements ToModel, WithStartRow, WithUpserts, WithUpsertColumns
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        try {
            return new TMS_Product([
                'pr_id'         => $row[0],
                'pr_vendor_amount'  => $row[1]
            ]);
        }catch (\Exception $e) {
            Log::error($e);
            dd($e);
        }
    }

    public function upsertColumns()
    {
        return ['pr_vendor_amount'];
    }

    public function startRow(): int
    {
        return 2; // 데이터 읽기가 시작될 첫 번째 행을 지정합니다. (여기서는 2번째 행부터 읽습니다.)
    }

    // 업셋용 유니크 키
    public function uniqueBy() {
        return "pr_id";
    }
}
