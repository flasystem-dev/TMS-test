<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use App\Models\TMS_ProductOption;

class ProductOptionImport implements ToModel, WithStartRow, WithUpserts
{
    /**
     * @param array $row
     *
     * @return TMS_ProductOption|null
     */
    public function model(array $row)
    {
        try {
            return new TMS_ProductOption([
                'pr_id'         => $row[0],
                'option_title'  => $row[1],
                'option_value'  => $row[2],
                'is_used'       => $row[3]
            ]);
        }catch (\Exception $e) {
            dd($row);
            Log::error($e);
        }
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
