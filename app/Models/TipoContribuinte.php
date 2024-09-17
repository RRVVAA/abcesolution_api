<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoContribuinte extends Model
{
    use HasFactory;
    protected $fillable     = ["id", "tipo_contribuinte"];
}
