<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model
{
    use HasFactory;

    protected $table = 'order_delivery';
    protected $primaryKey = 'order_idx';
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable=[
        'order_idx',
        'receiver_name', 'receiver_tel', 'receiver_phone',
        'delivery_date', 'delivery_time', 'delivery_post', 'delivery_address',
        'delivery_card', 'delivery_ribbon_left', 'delivery_ribbon_right', 'delivery_message',
        'delivery_state_code', 'delivery_state_code_before',
        'send_id', 'send_name', 'send_time', 'delivery_photo', 'delivery_photo2', 'delivery_photo3', 'delivery_com_time', 'delivery_insuName',
        'pr_idx', 'goods_name', 'is_balju'
    ];

    public function product() {
        return $this -> hasOne(TMS_Product::class, 'idx', 'pr_idx');
    }
}
