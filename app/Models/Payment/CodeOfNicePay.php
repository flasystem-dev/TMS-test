<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeOfNicePay extends Model
{
    use HasFactory;

    protected $table = 'code_of_nicepay_card_bank';

    protected $primaryKey = 'code_no';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable= ['code_no', 'code_name'];

    public static function codeName($code) {
        return CodeOfNicePay::find($code) -> code_name;
    }
}
