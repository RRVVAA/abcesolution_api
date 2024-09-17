<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IcmsEstado extends Model
{
    use HasFactory;
    protected $fillable = [
        'uf_origem', "uf_destino", 'aliquota_origem', 'aliquota_destino'
    ];
}
