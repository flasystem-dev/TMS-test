<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Facades\DB;

use App\Models\OrderPayment;
use App\Models\OrderData;

class OrderDataImport implements ToModel, WithStartRow, WithChunkReading, WithBatchInserts
{

    /**
     * @param array $row
     *
     * @return OrderData|null
     */
    public function model(array $row)
    {
        try {

//            $order = OrderData::find($row[0]);

            return new OrderData([
                'od_id'                   => $insert_id,
                'order_idx'            => $row[0],
                'mall_code'            => $order->order_number,
                'brand_type_code'       => 1,
                'order_number'         => $order->payment_state_code,
                'orderer_mall_id'    => $order->payment_type_code,
                'orderer_name'       => $order->payment_state_code == 'PSDN'? $order->total_amount : 0,
                'orderer_tel'         => $order->payment_state_code == 'PSDN'? $order->order_time : null,
                'orderer_phone'         => $row[1],

            ]);
        }catch (\Exception $e) {
            Log::error($e);
        }
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
