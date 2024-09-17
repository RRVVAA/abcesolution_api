<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NfeAutorizado extends Model
{
    use HasFactory;
    protected $fillable = [
        'nfe_id', 'aut_contato', 'aut_cnpj'
    ];
}
