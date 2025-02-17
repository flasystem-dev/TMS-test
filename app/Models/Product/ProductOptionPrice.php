<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductOptionPrice extends Model
{
    use HasFactory;

    protected $table = 'product_option_prices';
    protected $fillable = [
        'id', 'product_id', 'option_type_id', 'name', 'option_price', 'balju_option_price', 'vendor_option_price'
    ];

    public function optionType() {
        return $this->belongsTo('App\Models\Product\ProductOptionType', 'option_type_id');
    }

    public static function make_productOption($input) {
        $product_id = $input['id'];
        if(!empty($input['option_type_id'])) {
            $inputOptions = [];

            $optionTypes = $input['option_type_id'];
            $optionNames = $input['option_name'];
            $optionPrices = $input['option_price'];
            $baljuOptionPrice = $input['balju_option_price'];
            $vendorOptionPrice = $input['vendor_option_price'];

            foreach ($optionTypes as $key => $optionType) {
                $inputOptions[] = [
                    'option_type_id' => $optionType,
                    'name' => $optionNames[$key],
                    'option_price' => $optionPrices[$key],
                    'balju_option_price' => $baljuOptionPrice[$key],
                    'vendor_option_price' => $vendorOptionPrice[$key],
                ];
            }

            DB::transaction(function() use ($product_id, $inputOptions) {
                DB::table('product_option_prices')->where('product_id', $product_id)->delete();

                foreach ($inputOptions as $option) {
                    DB::table('product_option_prices')->insert([
                        'product_id' => $product_id,
                        'option_type_id' => $option['option_type_id'],
                        'name' => $option['name'],
                        'option_price' => $option['option_price'],
                        'balju_option_price' => $option['balju_option_price'],
                        'vendor_option_price' => $option['vendor_option_price']
                    ]);
                }
            });
        }else {
            DB::table('product_option_prices')->where('product_id', $product_id)->delete();
        }
    }
}
