<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DocumentCheck extends Model
{
    use HasFactory;
    protected $table = 'document_check';

    protected $primaryKey = 'order_idx';

    public $timestamps = false;

    protected $fillable= ['order_idx', 'tran_check', 'tax_check', 'tran_time', 'tax_time'];

    public static function transaction_check($encodedData) {
        $decodedData = base64_decode($encodedData);
        $data = json_decode($decodedData, true);

        foreach ($data['orders_idx'] as $idx) {
            DocumentCheck::updateOrCreate(
                ['order_idx' => $idx],
                ['tran_check' => 'Y', 'tran_time' => NOW()]
            );
        }
    }
}
