<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TributacaoIcms extends Model
{
    use HasFactory;
    protected $fillable = [  
        'natureza_operacao_id',
        'cstIcms',
        'cfop',
        'fcp',
        'modBC',
        'obsExICMS',
        'infAdFiscoRegraICMS',
       
    ];
}
