<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientManager extends Model
{
    use HasFactory;

    protected $table = 'clients_managers';
    protected $fillable=[
        'id', 'client', 'name', 'tel', 'email', 'fax', 'memo', 'is_default'
    ];

########################################################################################################################
########################################################################################################################

    ################################ 담당자 - 대표 지정 ###################################################
    public static function set_default($id) {
        $client_id = ClientManager::find($id)->value('client');
        $client = Client::find($client_id);

        if($client) {
            foreach ($client->managers as $manager) {
                if($manager->id === (int)$id) {
                    $manager -> is_default = 1;
                }else {
                    $manager -> is_default = 0;
                }
                $manager -> save();
            }
        }
    }
}
