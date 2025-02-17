<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductPrice extends Model
{
    use HasFactory;

    protected $table = 'product_prices';

    protected $fillable = [
        'id' , 'product_id', 'price_type_id', 'product_price', 'balju_price', 'vendor_price', 'created_at', 'updated_at'
    ];

    public static function make_productPrices($input) {
        $product_id = $input['id'];
        if(!empty($input['price_type'])){
            $inputPrices = [];

            $priceTypes = $input['price_type'];
            $productPrices = $input['product_price'];
            $baljuPrices = $input['balju_price'];
            $vendorPrices = $input['vendor_price'];

            foreach ($priceTypes as $key => $priceType) {
                $inputPrices[] = [
                    'price_type_id' => $priceType,
                    'product_price' => $productPrices[$key],
                    'balju_price' => $baljuPrices[$key],
                    'vendor_price' => $vendorPrices[$key]
                ];
            }

            DB::transaction(function() use ($product_id, $inputPrices) {
                DB::table('product_prices')->where('product_id', $product_id)->delete();

                foreach ($inputPrices as $price) {
                    DB::table('product_prices')->insert([
                        'product_id' => $product_id,
                        'price_type_id' => $price['price_type_id'],
                        'product_price' => $price['product_price'],
                        'balju_price'   => $price['balju_price'],
                        'vendor_price' => $price['vendor_price']
                    ]);
                }

            });
        }else {
            DB::table('product_prices')->where('product_id', $product_id)->delete();
        }
    }
}
