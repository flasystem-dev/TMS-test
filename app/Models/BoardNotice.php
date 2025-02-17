<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Board;

class BoardNotice extends Model
{
    use HasFactory;

    protected $fillable = ['id','brand','title','contents','writer','hit',
        'file1','file1_name','file1_path', 'file2', 'file2','file2_path',
        'name','user_id', 'is_used'
    ];

    public function board() {
        return Board::firstWhere('type', 'notice');
    }
}
