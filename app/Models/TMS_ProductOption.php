<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TMS_ProductOption extends Model
{
    use HasFactory;

    protected $table = 'tms_products_option';

    public $timestamps = false;

    protected $hidden = ['option_value'];

    protected $fillable=[
        'pr_id', 'option_title', 'option_value', 'is_used'
    ];

    public static function product_upert($input) {
        $options = $input['option_title'];

        foreach ($options as $key => $value) {
            if(empty($value)) {
                continue;
            }
            if(TMS_ProductOption::where('pr_id', $input['pr_id']) -> where('option_title', $value) -> exists()) {
                $option = TMS_ProductOption::where('pr_id', $input['pr_id']) -> where('option_title', $value) -> first();
            }else {
                $option = new TMS_ProductOption();
            }

            $option -> option_title = $value;
            $option -> pr_id = $input['pr_id'];
            $names = $input['option_name'.$key];
            $prices = $input['option_price'.$key];
            $data = array();
            foreach ($names as $key2 => $value2) {
                $data[$value2] = $prices[$key2];
            }
            $json = json_encode($data, JSON_UNESCAPED_UNICODE);

            $option -> option_value = $json;
            $option -> save();
        }
        // 존재하지 않는 옵션 삭제
        self::check_delete($input);
    }

    // 존재하지 않는 옵션 삭제
    public static function check_delete($input) {
        $options = TMS_ProductOption::where('pr_id', $input['pr_id']) -> get();

        foreach ($options as $option) {
            if(!in_array($option->option_title, $input['option_title'])){
                $delete_option = TMS_ProductOption::find($option -> id);
                $delete_option -> delete();
            }
        }
    }
}
