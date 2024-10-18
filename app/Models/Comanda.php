<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comanda extends Model
{
    use HasFactory;
    protected $fillable =[
        'empresa_id',
        'status_id',
        'identificacao',
    ];

    public function empresa(){
        return $this->belongsTo(Empresa::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }

}
