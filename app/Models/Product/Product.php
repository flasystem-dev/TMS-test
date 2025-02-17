<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

use App\Models\Product\ProductPrice;
use App\Models\Product\ProductOptionPrice;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id' , 'code', 'ctgyA', 'ctgyB', 'ctgyC', 'type', 'name', 'brand', 'img', 'thumbnail', 'description', 'is_popular', 'is_discount', 'delivery_type', 'is_used',
        'memo', 'search_words'
    ];

    public function options() {
        return $this->hasMany(ProductOptionPrice::class, 'product_id', 'id');
    }

    public function groupedOptions()
    {
        return $this->options
            ->groupBy('option_type_id') // option_type_id별로 그룹화
            ->map(function ($items) {
                return $items->map(function ($item) {
                    // 각 옵션을 배열로 변환
                    return $item->toArray();
                })->toArray(); // 중첩 배열로 변환
            })
            ->values() // 그룹의 인덱스를 0부터 재정렬
            ->toArray(); // 최종 배열로 변환
    }

    public function prices() {
        return $this->hasMany(ProductPrice::class, 'product_id', 'id') -> orderBy('price_type_id');
    }

    public function get_price($type) {
        return ProductPrice::where('product_id', $this->id) -> where('price_type_id', $type) -> first();
    }
    
}
