<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractCompany extends Model
{
    use HasFactory;

    protected $table = 'contract_company';
    protected $fillable=[
        'brand_type_code', 'com_name', 'com_name_memo', 'phone1', 'phone1_memo', 'phone2', 'phone2_memo', 'fax', 'email', 'tax_email',
        'com_business_num', 'ceo_name', 'com_addr', 'com_type', 'com_kind', 'memo', 'discount', 'deposit', 'contract_date', 'created_at', 'updated_at'
    ];
}
