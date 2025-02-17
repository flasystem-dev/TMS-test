<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TotalInformations extends Model
{
    use HasFactory;

    protected $table = 'total_information';

    protected $fillable = [
        'brand', 'mall_code', 'date_type', 'year', 'month',
        'order_count', 'order_amount', 'pay_amount', 'misu_amount', 'vendor_amount', 'option_amount', 'card_amount',
        'created_at', 'updated_at'
    ];

}
