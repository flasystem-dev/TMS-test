<?php
namespace App\Services\Product;

use App\Models\Product\Product;

class ProductService
{
    public static function get_productsForIndex($search)
    {
        $query = Product::with('prices');

        if(isset($search['brand'])) {
            $query -> whereIn('brand', $search['brand']);
        }

        if(isset($search['category1'], $search['category2'])) {
            $ctgy_column = "ctgy".$search['category1'];
            $query -> where($ctgy_column, 'like', "%".$search['category2']."%");
        }

        if(isset($search['name'])) {
            $query -> where('name', 'like', '%'.$search['name'].'%');
        }

        $view_check = $search['is_not_view'] ?? 0;
        $used_check = $search['is_not_use'] ?? 0;
        if(!$view_check) {
            $query -> where('is_view', 1);
        }
        if(!$used_check) {
            $query -> where('is_used', 1);
        }

        return $query->get();
    }
}