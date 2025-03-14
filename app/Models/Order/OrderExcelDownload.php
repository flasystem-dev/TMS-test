<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderExcelDownload extends Model
{
    use HasFactory;

    protected $table = 'order_excel_download';
    protected $fillable = ['file_name', 'file_url', 'status', 'progress', 'requester', 'completed_time', 'created_at', 'updated_at'];
}
