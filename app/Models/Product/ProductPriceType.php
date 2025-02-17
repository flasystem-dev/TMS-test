<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPriceType extends Model
{
    use HasFactory;

    protected $table = 'product_price_types';
    protected $fillable = [
        'id', 'name'
    ];

    
}
