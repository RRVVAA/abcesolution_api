<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    protected $primaryKey   = "id_certificado";
    protected $table        ="certificado";
    protected $fillable     = [
        "id_certificado",
        "arquivo_binario",
        "nome_arquivo",
        "senha",
        "cnpj"
    ];
}
