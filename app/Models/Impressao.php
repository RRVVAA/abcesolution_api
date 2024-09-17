<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Impressao extends Model
{
    use HasFactory;
    protected $fillable = [
        'chave', 'num_pdv_id'
    ];
}
