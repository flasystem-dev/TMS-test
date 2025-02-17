<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TMS_Category extends Model
{
    use HasFactory;

    protected $table = 'tms_ctgy';

    protected $fillable = [
        'ct1', 'ct2', 'ct3', 'ct_name', 'created_at', 'updated_at', 'ct_handler', 'is_used'
    ];

    public static function get_name($ct) {

        $ct1 = Str::substr($ct, 0, 1);
        $ct2 = str_replace($ct1, '', $ct);

        $ctgy = TMS_Category::where('ct1', $ct1) -> where('ct2', $ct2) -> first();

        return $ctgy -> ct_name;
    }
}
