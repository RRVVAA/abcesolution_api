<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMovimento extends Model
{
    use HasFactory;
    protected $fillable     = ["id", "tipo_movimento", "ent_sai","movimenta_estoque"];
}
