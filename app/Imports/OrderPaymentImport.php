<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Facades\DB;

use App\Models\OrderPayment;
use App\Models\OrderData;

class OrderPaymentImport implements ToModel, WithStartRow, WithUpserts, WithChunkReading, WithBatchInserts
{
    private static $id = 65000;

//    use RemembersRowNumber;

    /**
     * @param array $row
     *
     * @return OrderPayment|null
     */
    public function model(array $row)
    {
        try {
//            $currentRowNumber = $this->getRowNumber();

            $insert_id = self::$id++;

            $order = OrderData::find($row[0]);

            return new OrderPayment([
                'id'                    => $insert_id,
                'order_idx'             => $row[0],
                'order_number'          => $order->order_number,
                'payment_number'        => 1,
                'payment_state_code'    => $order->payment_state_code,
                'payment_type_code'     => $order->payment_type_code,
                'payment_amount'        => $order->payment_state_code == 'PSDN'? $order->total_amount : 0,
                'payment_time'          => $order->payment_state_code == 'PSDN'? $order->order_time : null,
                'deposit_name'          => $row[1]
            ]);
        }catch (\Exception $e) {
            Log::error($e);
        }
    }

    public function startRow(): int
    {
        return 2; // 데이터 읽기가 시작될 첫 번째 행을 지정합니다. (여기서는 2번째 행부터 읽습니다.)
    }

    // 업셋용 유니크 키
    public function uniqueBy() {
        return "order_idx";
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
