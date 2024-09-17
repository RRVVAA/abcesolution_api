<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NfeReferenciado extends Model
{
    use HasFactory;
    protected $fillable = [
        'nfe_id',
        'tipo_nota_referenciada',
        'ref_NFe',
        'ref_ano_mes',
        'ref_num_nf',
        'ref_serie'
    ];
}
