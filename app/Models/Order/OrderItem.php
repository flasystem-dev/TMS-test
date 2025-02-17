<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Order\OrderItemOption;
use App\Models\Product\Product;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';
    protected $fillable = [
        'id', 'order_id', 'product_id', 'price_type_id', 'product_name', 'quantity', 'item_total_amount', 'product_price', 'balju_price', 'vendor_price', 'options_amount', 'balju_options_amount', 'vendor_options_amount'
    ];

    public function options()
    {
        return $this -> hasMany(OrderItemOption::class, 'order_item_id', 'id') -> orderBy('option_type_id','asc');
    }

    public function product()
    {
        return $this -> hasOne(Product::class, 'id', 'product_id');
    }
}
