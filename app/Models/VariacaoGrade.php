<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Tenant\Traits\EmpresaTrait;

class VariacaoGrade extends Model
{
    use HasFactory;
    protected $fillable = [
         'empresa_id',
        'variacao',
    ];
}
