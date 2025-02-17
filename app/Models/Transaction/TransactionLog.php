<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    use HasFactory;
    protected $table = 'transaction_log';
    public $timestamps = false;

    protected $fillable= ['brand', 'com_name', 'link', 'created_at', 'email', 'handler'];

    public static function createLog($data) {
        TransactionLog::create([
            'brand' => $data['brand'],
            'com_name'=> $data['com_name'],
            'link' => $data['link'],
            'created_at' => NOW(),
            'email' => $data['email'],
            'handler' => $data['handler']
        ]);
    }
}
