<?php

namespace App\Imports;

use App\Models\MallMonthlyEtcPrice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Log;

class MonthlyEtcPriceImport implements ToModel, WithStartRow, SkipsEmptyRows
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;

    }
    /**
    * @param array $row
    *
    * @return MallMonthlyEtcPrice
     */
    public function model(array $row)
    {
        try {
            return MallMonthlyEtcPrice::updateOrCreate(
                [
                    'mall_code' => $row[0],
                    'year'      => $this->year,
                    'month'     => $this->month,
                ],
                [
                    'card_charge'   => null,
                    'deposit_price' => null,
                    'etc1'          => $row[3],
                    'etc2'          => $row[4],
                    'etc3'          => $row[5],
                ]
            );

        }catch (\Exception $e) {
            Log::error('Error processing row', ['exception' => $e, 'row' => $row]);
            return null; // 이 부분이 추가되었습니다.
        }
    }

    public function startRow(): int
    {
        return 2; //2행부터 읽기
    }

//    public function uniqueBy()
//    {
//        return ['mall_code', 'year', 'month'];
//    }

}
