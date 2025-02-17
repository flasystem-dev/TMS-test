<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardEvent extends Model
{
    use HasFactory;

    protected $fillable = ['id','brand','title','contents','writer', 'start_period','end_period','hit',
        'file1','file1_name','file1_path', 'file2', 'file2','file2_path',
        'name','user_id', 'is_used'
    ];

    public function board() {
        return Board::firstWhere('type', 'event');
    }
}
