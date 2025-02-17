<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemOption extends Model
{
    use HasFactory;

    protected $table = 'order_item_options';
    protected $fillable = [
        'id', 'order_item_id', 'option_type_id', 'option_type_name', 'option_price_id', 'option_name', 'option_price', 'balju_option_price', 'vendor_option_price', 'is_view'
    ];
    
}
