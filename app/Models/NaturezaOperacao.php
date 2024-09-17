<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NaturezaOperacao extends Model
{
    use HasFactory;
    protected $fillable = [
        "descricao",
        "tipo",
        "indPres",
        'finNFe',
        "devolucao",
        "padrao",
        "cstIcms",
        "cfop",
        "modBC",
        "pICMS",
        "pRedBC",
        "modBCST",
        "vBCST",
        "pICMSST",
        "pMVAST",
        "pRedBCST",
        "vICMSSubstituto",
        "pFCP",
        "pFCPST",
        "pFCPSTRet",
        "pDif",
        "cstIpi",
        "pIPI",
        "vBCIPI",
        "tipo_calc_ipi",
        "CNPJProd",
        "cSelo",
        "qSelo",
        "qUnidIPI",
        "vUnidIPI",
        "cEnq",
        "cstPis",
        "tipo_calc_pis",
        "pPIS1",
        "vAliqProd_pis",
        "pPISST",
        "vAliqProd_pisst",
        "cstCofins",
        "tipo_calc_cofins",
        "pCofins",
        "vAliqProd_cofins",
        "tipo_calc_cofinsst",
        "vAliqProd_cofinsst",
        "obs",
        "infAdFisco",
    ];
    
    public function tributacoes(){
        return $this->hasMany(Tributacao::class, 'natureza_operacao_id', 'id');
    }
    
   
    
}
