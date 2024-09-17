<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NfeTransporte extends Model
{
    use HasFactory;
    protected $fillable = [
        'nfe_id',
        'modFrete',
        'transp_xNome',
        'transp_IE',
        'transp_xEnder',
        'transp_xMun',
        'transp_UF',
        'transp_CNPJ',
        'transp_placa',
        'UF_placa',
        'RNTC',
        'qVol',
        'esp',
        'marca',
        'nVol',
        'pesoL',
        'pesoB',
        'transp_vagao',
        'transp_balsa'
    ];
}
