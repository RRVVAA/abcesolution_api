<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NfeDuplicata extends Model
{
    use HasFactory;
    protected $fillable = [
        'nfe_id',
        'nDup',
        'dVenc',
        'vDup'
    ];
}
