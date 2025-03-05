<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDataDeleted extends Model
{
    use HasFactory;

    protected $table = 'order_data_deleted';

    const CREATED_AT = 'create_ts';
    const UPDATED_AT = 'update_ts';
    protected $fillable=[
        'od_id','order_idx','mall_code', 'client_id', 'brand_type_code','order_number',
        'order_time','orderer_mall_id','orderer_name','orderer_tel','orderer_phone','orderer_email',
        'order_quantity','payment_type_code','payment_state_code','payment_date',
        'total_amount','pay_amount','discount_amount','refund_amount','misu_amount','supply_amount', 'balju_amount',
        'admin_regist','admin_memo',
        'create_ts','update_ts',
        'options_string','options_parse_yn','options_string_display','options_type',
        'order_claim','order_claim_memo','open_market_goods_url','db_num',
        'new_order_yn', 'is_view', 'is_highlight', 'is_alim',
        'receiver_name', 'receiver_tel', 'receiver_phone',
        'delivery_date', 'delivery_time', 'delivery_post', 'delivery_address',
        'delivery_card', 'delivery_ribbon_left', 'delivery_ribbon_right', 'delivery_message',
        'delivery_state_code', 'delivery_state_code_before',
        'send_id', 'send_name', 'send_time', 'delivery_photo', 'delivery_photo2', 'delivery_photo3', 'delivery_com_time', 'delivery_insuName',
        'pr_idx', 'goods_name', 'is_balju'
    ];
}
