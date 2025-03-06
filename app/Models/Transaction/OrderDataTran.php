<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderDataTran extends Model
{
    use HasFactory;
    protected $table = 'order_data_tran';

    protected $primaryKey = 'order_idx';

    public $timestamps = false;

    protected $fillable= ['order_idx', 'brand_type_code', 'mall_code', 'client_id', 'total_amount', 'order_time', 'delivery_date', 'orderer_name', 'orderer_phone', 'reciever_name', 'reciever_phone', 'delivery_ribbon_left', 'delivery_address', 'payment_name', 'goods_name', 'etc', 'create_ts','is_tran'];

    public static function orderTranUpdate($encodedData) {
        $decodedData = base64_decode($encodedData);
        $data = json_decode($decodedData, true);
        foreach ($data['orders_idx'] as $idx) {

        }
    }
}
