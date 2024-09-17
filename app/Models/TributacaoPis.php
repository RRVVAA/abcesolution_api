<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TributacaoPis extends Model
{
    use HasFactory;
    protected $fillable = [
        'natureza_operacao_id',
        'cstPis',
        'aliquota_pis',
        'base_pis',
        'obsExPIS',
    ];
}
