<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TributacaoIva extends Model
{
    use HasFactory;
    protected $fillable = [
        'natureza_operacao_id',
        'estado_id',
        'tributacao_id',
        'uf_origem',
        'uf_destino',
        'pIcmsInter',
        'pIcmsIntra',
        'pMVAST',
        'pMVASTImportado',
        'pDifal',
        'pRedBCST',
        'pFCPST',
        'modBCST',
        'pFCPSTRet'
    ];
}
