<?php

namespace App\DTOs;

use Illuminate\Support\Facades\DB;
use App\Models\Product\Product;

class OrderProduct
{
    public int $product_id;
    public string $product_code;
    public string $product_type;
    public string $product_name;
    public string $product_img;
    public int $price_type_id;
    public int $item_total_amount;
    public int $product_price;
    public int $balju_price;
    public int $vendor_price;
    public int $options_amount;
    public int $balju_options_amount;
    public int $vendor_options_amount;

    public array $options;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function toArray() {
        return get_object_vars($this); // 객체의 모든 속성을 배열로 변환
    }

    public static function makeProductFromInput($input)
    {
        $product_price = $input['product_price'] ?? 0;
        $options_amount = 0;
        $balju_options_amount = 0;
        $vendor_options_amount = 0;
        $options = [];


        if(isset($input['option_type_id'])) {
            $option = [];
            foreach ($input['option_type_id'] as $key => $value) {
                $option['option_type_id'] = $value ?? 8;
                $option['option_type_name'] = $input['option_type_name'][$key] ?? "기타옵션";
                $option['option_price_id'] = $input['option_price_id'][$key] ?? 0;
                $option['option_name'] = $input['option_name'][$key] ?? "";
                $option['option_price'] = $input['option_price'][$key] ?? 0;
                $option['balju_option_price'] = $input['balju_option_price'][$key] ?? 0;
                $option['vendor_option_price'] = $input['vendor_option_price'][$key] ?? 0;
                $options[] = $option;

                $options_amount += $option['option_price'];
                $balju_options_amount += $option['balju_option_price'];
                $vendor_options_amount += $option['vendor_option_price'];
            }
        }

        return new self([
            'product_id'            => $input['product_id'] ?? 0,
            'product_code'          => $input['product_code'] ?? "",
            'product_type'          => $input['product_type'] ?? "",
            'product_name'          => $input['product_name'] ?? "",
            'product_img'           => $input['product_img'] ?? "",
            'price_type_id'         => $input['price_type_id'] ?? 0,
            'item_total_amount'     => $product_price + $options_amount,
            'product_price'         => $product_price,
            'balju_price'           => $input['balju_price'] ?? 0,
            'vendor_price'          => $input['vendor_price'] ?? 0,
            'options_amount'        => $options_amount,
            'balju_options_amount'  => $balju_options_amount,
            'vendor_options_amount' => $vendor_options_amount,
            'options'               => $options
        ]);
    }

    public static function makeProductFromOrder($order)
    {
        $product = DB::table('products') -> select('id', 'code', 'type', 'name', 'thumbnail') -> where('id', $order->item->product_id) -> first();

        $orderProduct = new OrderProduct();
        $orderProduct->product_id               = $product->id;
        $orderProduct->product_code             = $product->code;
        $orderProduct->product_type             = $product->type;
        $orderProduct->product_name             = $product->name;
        $orderProduct->product_img              = $product->thumbnail;
        $orderProduct->price_type_id            = $order->item->price_type_id;
        $orderProduct->item_total_amount        = $order->item->item_total_amount;
        $orderProduct->product_price            = $order->item->product_price;
        $orderProduct->balju_price              = $order->balju_amount;
        $orderProduct->vendor_price             = $order->vendor_amount;
        $orderProduct->options_amount           = $order->item->options_amount;
        $orderProduct->balju_options_amount     = $order->item->balju_options_amount;
        $orderProduct->vendor_options_amount    = $order->item->vendor_options_amount;
        $orderProduct->options                  = [];

        if($order->item->options->isNotEmpty()) {
            foreach ($order->item->options as $option) {
                $orderProduct -> options[] =
                    [
                        'option_type_id' => $option->option_type_id ?? 0,
                        'option_type_name' => $option->option_type_name,
                        'option_price_id' => $option->option_price_id ,
                        'option_name' => $option->option_name,
                        'option_price' => $option->option_price,
                        'balju_option_price' => $option->balju_option_price,
                        'vendor_option_price' => $option -> vendor_option_price
                    ];

            }
        }
        return $orderProduct;
    }

    public static function makeProduct($product_id, $price_type_id, $product_price)
    {
        $product = DB::table('products') -> select('id', 'code', 'type', 'name', 'thumbnail') -> where('id', $product_id) -> first();
        $productPrice = DB::table('product_prices') -> select('product_price', 'balju_price', 'vendor_price') -> where('product_id', $product_id) -> where('price_type_id', $price_type_id) -> first();

        if($product) {
            return new self([
                'product_id'            => $product->id,
                'product_code'          => $product->code,
                'product_type'          => $product->type,
                'product_name'          => $product->name,
                'product_img'           => $product->thumbnail,
                'price_type_id'         => $price_type_id,
                'item_total_amount'     => $product_price,
                'product_price'         => $product_price,
                'balju_price'           => $productPrice -> balju_price ?? 0,
                'vendor_price'          => $productPrice -> vendor_price ?? 0,
                'options_amount'        => 0,
                'balju_options_amount'  => 0,
                'vendor_options_amount' => 0,
                'options'               => []
            ]);
        }
        return null;
    }

    // json 객체로 변환
    public static function fromJson($json) {
        $decoded = json_decode($json, true);
        return new self($decoded);
    }

    // 옵션 만들기
    public static function makeOption(OrderProduct $orderProduct, $option_id) {

        $option_price = DB::table('product_option_prices') -> where('id', $option_id) ->first();

        if($option_price) {
            $orderProduct -> item_total_amount += $option_price -> option_price;
            $orderProduct -> options_amount += $option_price -> option_price;
            $orderProduct -> balju_options_amount += $option_price -> balju_option_price;
            $orderProduct -> vendor_options_amount += $option_price -> vendor_option_price;
            $orderProduct -> options[] =
             [
                'option_type_id' => $option_price->option_type_id ?? 0,
                'option_type_name' => optionTypeName($option_price->option_type_id),
                'option_price_id' => $option_id ,
                'option_name' => $option_price -> name,
                'option_price' => $option_price -> option_price,
                'balju_option_price' => $option_price -> balju_option_price,
                'vendor_option_price' => $option_price -> vendor_option_price
            ];
        }
        return $orderProduct;
    }

    /* $location = "시도/시군구" */
    public static function makeLocationOption(OrderProduct $orderProduct, $location) {
        [$sido, $sigungu] = explode('/', $location);

        $product_type = $orderProduct -> product_type;
        $add_price = DB::table('loc_add_price') -> where('sido',$sido) -> where('sigungu',$sigungu) -> value($product_type);

        if(!empty($add_price)) {
            $orderProduct -> item_total_amount += $add_price;
            $orderProduct -> options_amount += $add_price;
            $orderProduct -> balju_options_amount += $add_price;
            $orderProduct -> vendor_options_amount += $add_price;

            $orderProduct -> options[] =
                [
                    'option_type_id' => 9,
                    'option_type_name' => "지역추가금",
                    'option_name' => $sido . " " . $sigungu,
                    'option_price' => $add_price,
                    'balju_option_price' => $add_price,
                    'vendor_option_price' => $add_price
                ];
        }
        return $orderProduct;
    }

    // 커스텀 옵션
    public static function makeCustomOption(OrderProduct $orderProduct, $name, $option_price) {
        $orderProduct -> item_total_amount += $option_price;
        $orderProduct -> options_amount += $option_price;
        $orderProduct -> balju_options_amount += $option_price;
        $orderProduct -> vendor_options_amount += $option_price;

        $orderProduct -> options[] =
            [
                'option_type_id' => 8,
                'option_type_name' => "기타옵션",
                'option_name' => $name,
                'option_price' => $option_price,
                'balju_option_price' => $option_price,
                'vendor_option_price' => $option_price
            ];
        return $orderProduct;
    }

    // 기타 옵션 ( 발주용 )
    public static function makeEtcOption(OrderProduct $orderProduct, $name, $vendor_option_price, $balju_option_price) {
        $orderProduct -> balju_options_amount += $balju_option_price;
        $orderProduct -> vendor_options_amount += $vendor_option_price;

        $orderProduct -> options[] =
            [
                'option_type_id' => 10,
                'option_type_name' => "기타",
                'option_name' => $name,
                'option_price' => 0,
                'balju_option_price' => $balju_option_price,
                'vendor_option_price' => $vendor_option_price,
                'is_view' => 0
            ];
        return $orderProduct;
    }
}