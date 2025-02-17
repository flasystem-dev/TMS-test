<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOptionType extends Model
{
    use HasFactory;

    protected $table = 'product_option_types';
    protected $fillable = [
        'id', 'product_id', 'name', 'is_view'
    ];
}
