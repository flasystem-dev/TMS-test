<?php
namespace App\Services\Client;

use Illuminate\Support\Facades\DB;

use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemOption;
use App\Models\Client;

class ClientService
{
    public static function getClients($search)
    {
        $clients = Client::query();

        if($search) {

            if($search['brand']!=="all") {
                $clients->where('brand', 'like', "%".$search['brand']."%");
            }

            if(isset($search['search'])){
                $search_column = [
                    'name',
                    'tel',
                    'ceo_name',
                    'business_number',
                    'memo',
                    'search_words'
                ];

                $word = $search['search_word'];

                if($search['search']!== "all") {
                    switch ($search['search']) {
                        case 'tel':
                        case 'business_number':
                            $clients->whereRaw("REPLACE(" . $search['search'] . ", '-', '') LIKE ?", ["%" . str_replace('-', '', $word) . "%"]);
                            break;
                        case 'search_words':
                            $clients->whereRaw("JSON_SEARCH(search_words, 'one', ?) IS NOT NULL", ["%$word%"]);
                            break;
                        default:
                            $clients->where($search['search'], "like", "%$word%");
                    }
                }else {

                    $clients -> where(function($query) use ($search_column, $word){
                        foreach ($search_column as $column) {
                            switch ($search_column) {
                                case 'tel':
                                case 'business_number':
                                    $query->orWhereRaw("REPLACE(" . $column . ", '-', '') LIKE ?", ["%" . str_replace('-', '', $word) . "%"]);
                                    break;
                                default:
                                    $query->orWhere($column, "like", "%$word%");

                            }

                        };
                    });
                }
            }
        }
        return $clients -> get();
    }
}