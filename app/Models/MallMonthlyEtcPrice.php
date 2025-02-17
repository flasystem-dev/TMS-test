<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MallMonthlyEtcPrice extends Model
{
    use HasFactory;
    protected $table = 'mall_monthly_etc_price';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable=['mall_code','year','month','card_charge','deposit_price','etc1','etc2','etc3'];
}
