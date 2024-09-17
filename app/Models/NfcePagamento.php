<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NfcePagamento extends Model
{
    use HasFactory;
    protected $fillable = [
        'nfce_id', 'tPag', 'vPag'
    ];
}
