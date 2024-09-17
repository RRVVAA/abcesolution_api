<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TributacaoCofins extends Model
{
    use HasFactory;
    protected $fillable = [ 
        'natureza_operacao_id',
        'cstCofins',
        'aliquota_cofins',
        'base_cofins',
        'obsExCOFINS',
        'infAdFiscoRegraCOFINS',
    ];
}
