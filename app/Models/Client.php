<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\ClientManager;
use App\Models\User;

class Client extends Model
{
    use HasFactory;

    protected $casts = [
        'search_words' => 'json',
    ];

    protected $fillable=[
        'id', 'brand', 'name', 'tel', 'email', 'fax', 'address', 'ceo_name', 'business_number', 'tax_business_number', 'business_type','business_kind',
        'assurance', 'assurance_amount', 'assurance_ex_date', 'contract', 'charge_ex_date', 'memo', 'is_valid', 'search_words', 'created_at', 'updated_at'
    ];

    public function managers() {
        return $this->hasMany(ClientManager::class, 'client');
    }

    public function users() {
        return $this->hasMany(User::class, 'client_id');
    }

    public function getContractFileNameAttribute()
    {
        if(!empty($this->contract)) {
            return collect(explode('/', $this->contract))->last();
        }
        return "";
    }
########################################################################################################################
########################################################################################################################

    ################################ 거래처 브랜드 설정 - 지정된 유저 기준 ###################################################
    public static function set_brand($id) {
        if($id) {
            $client = Client::find($id);

            if($client) {
                $brand = "";
                foreach ($client->users as $user) {
                    if(!Str::contains($brand, $user -> brand)) {
                        $brand .= "/" . $user -> brand;
                    }
                }

                // 앞, 뒤에 "/"가 있으면 제거
                $client->brand = trim($brand, '/');
            }
            $client -> save();
        }
    }

}
