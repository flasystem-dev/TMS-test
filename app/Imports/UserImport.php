<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Vendor;

class UserImport implements ToModel, WithStartRow, WithUpserts, WithChunkReading, WithBatchInserts
{

    private static $id = 1000;


    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        try {

            $insert_id = self::$id++;

            $user_type = '';

            if(strlen($row[1])===4){
                $brand = $row[1];
            }else {
                $brand = substr($row[1], 0, 4);
                $type = substr($row[1], -1, 1);
                $user_type = $type == "C" ? "IN" : "CO";
            }

            return new User([
                'id'                    => $insert_id,
                'vendor_idx'            => $row[0],
                'brand'                 => $brand,
                'user_id'               => $row[2],
                'name'                  => $row[3],
                'tel'                   => $row[4],
                'phone'                 => $row[5],
                'email'                 => $row[6],
                'address'               => $row[7],
//                'password'              => $row[8],
                'created_at'            => $row[8],
                'status'                => 1,
                'is_vendor'             => $row[11],
                'is_credit'             => 0,
                'user_type'             => $user_type,
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
        return "id";
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
