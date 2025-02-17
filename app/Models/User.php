<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $fillable = [ 'id',
        'vendor_idx', 'client_id', 'manager_id', 'brand', 'user_id', 'name', 'tel', 'phone', 'email', 'address', 'password', 'created_at', 'status', 'is_vendor', 'ban', 'is_credit',
        'memo', 'document_type', 'document_number', 'birth', 'auth'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    public function IsVendor()
    {
        if ($this->is_vendor == 2) {
            return true;
        }
        return false;
    }

    public function channel() {
        if ($this->brand === "BTFC" || $this->brand === "BTCS") {
            return DB::table('vendor') -> where('idx', $this->vendor_idx) -> value('rep_name');
        }

        return DB::table('vendor_pass') -> where('idx', $this->vendor_idx) -> value('name');
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

########################################################################################################################
########################################################## 조회 #########################################################

    ########################################## 유저 리스트 - index #######################################################
    public static function user_list($search) {

        if($search) {
            $users = User::query();
            $users->where('brand',$search['brand']);

            switch ($search['brand']) {
                case "BTCS":
                case "BTFC":
                    $users -> join('vendor', 'vendor.idx', '=', 'users.vendor_idx');
                    $users -> select('users.*', 'vendor.rep_name as channel_name');
                    break;
            }

            if(!(isset($search['all_status']) && $search['all_status'])) {
                $users -> where('users.status', 1);
            }

            if(isset($search['search'])){
                $search_column = [
                    'name',
                    'tel',
                    'phone',
                    'memo'
                ];


                if($search['search']!== "all") {
                    $users->whereRaw("REPLACE(" . $search['search'] . ", '-', '') LIKE ?", ["%" . str_replace('-', '', $search['search_word']) . "%"]);
                }else {

                    $users -> where(function($query) use ($search_column,$search){
                        foreach ($search_column as $column) {
                            $query->orWhereRaw(
                                "REPLACE(" . $column . ", '-', '') LIKE ?",
                                ["%" . str_replace('-', '', $search['search_word']) . "%"]
                            );
                        };
                    });
                }
            }

            return $users -> get();

        }

    }

}
