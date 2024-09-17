<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TributacaoIpi extends Model
{
    use HasFactory;
    protected $fillable = [  
        'natureza_operacao_id',
        'cstIpi',
        'aliquota_ipi',
        'base_ipi',
        'cEnq',
        'obsExIPI',
        'infAdFiscoRegraIPI',
       
    ];
}
