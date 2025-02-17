<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TalkTemplate extends Model
{
    use HasFactory;

    protected $table = 'popbill_template_info';
    protected $primaryKey = 'templateCode';
    public $incrementing = false;

    protected $fillable=[
        'idx', 'brand_type_code', 'plusFriendID', 'templateName', 'template', 'templateCode', 'variables', 'values'
        ];
}
