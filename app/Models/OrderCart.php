<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderCart extends Model
{
    use HasFactory;

    protected $table = 'order_cart';

    public $timestamps = false;

    protected $fillable=[
            'order_idx', 'option_name', 'option_price', 'option_status', 'is_settle', 'created_at'
        ];

    public static function insert_data($input){
        $options = $input['option'];

        foreach ($options as $option) {
            $option_arr = explode("/", $option);
            if(Str::contains($option_arr[0], '추가금') && $option_arr[2] == "0") {
                continue;
            }
            OrderCart::create([
                'order_idx' => $input['order_idx'],
                'option_name' => $option_arr[0].":".$option_arr[1],
                'option_price' => $option_arr[2],
                'balju_option_price' => $option_arr[2],
                'vendor_option_price' => $option_arr[2]
            ]);
        }
    }
}