<?php

namespace App\Imports;

use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Crypt;

class VendorImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $vendor = Vendor::find($row[0]);

        if ($vendor) {
            $vendor -> rr_number1 = $row[1];
            $vendor -> rr_number2 = !empty($row[2]) ? Crypt::encryptString($row[2]) : "";
            $vendor -> rep_addr = $row[3];
            $vendor -> company_name = $row[4];
            $vendor -> business_number = $row[5];
            $vendor -> company_addr = $row[6];
            $vendor -> save();
        }

        return null;
    }
}
