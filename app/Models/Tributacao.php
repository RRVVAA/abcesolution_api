<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tributacao extends Model
{
    
    protected $fillable =[
        'natureza_operacao_id',
        'descricao',
        'padrao',
        'cfop',
        'cfop_fora',
        'cfop_fora_contribuinte',
        'cfop_exportacao',
        'cstICMS',
        'vBCICMS',
        'modBC',
        'pICMS',
        'pRedBC',
        'modBCST',
        'vBCST',
        'pICMSST',
        'pMVAST',
        'pRedBCST',
        'vICMSSubstituto',
        'pFCP',
        'pFCPST',
        'vFCP',
        'pFCPSTRet',
        'pDif',
        'cstIPI',
        'pIPI',
        'vBCIPI',
        'tipo_calc_ipi',
        'CNPJProd',
        'cSelo',
        'qSelo',
        'qUnidIPI',
        'vUnidIPI',
        'pCOFINS',
        'pCOFINSST',
        'preco_unit_Pauta_ST',
        'motDesICMS',
        'pBCOp',
        'cEnq',
        'vIPI',
        'cstPIS',
        'tipo_calc_pis',
        'pPIS',
        'vAliqProd_pis',
        'pPISST',
        'vAliqProd_pisst',
        'vPIS',
        'vPISST',
        'cstCOFINS',
        'pFCPSTRet',
        'tipo_calc_cofins',
        'pCofins',
        'vAliqProd_cofins',
        'tipo_calc_cofinsst',
        'pCofinsst',
        'vAliqProd_cofinsst',
        'vCofins',
        'uso_consumo',
        'vbc_somente_produto','vbc_frete','vbc_ipi','vbc_outros', 'vbc_seguro','vbc_desconto',
        'ipi_somente_produto','ipi_frete','ipi_outros','ipi_seguro','ipi_desconto',
        'pis_somente_produto','pis_frete','pis_ipi','pis_outros','pis_seguro','pis_desconto',
        'cofins_somente_produto','cofins_frete','cofins_ipi','cofins_outros','cofins_seguro','cofins_desconto',
        'cst900_icms','cst900_redbc','cst900_credisn','cst900_st','cst900_redbcst',
    ];
    
    
    public static function getTributacaoPadrao($natureza_operacao_id, $produto_id){
        $tributacao_geral   = Tributacao::where(["natureza_operacao_id" =>$natureza_operacao_id,"padrao"=>"S"])->first();        
        $tributaProduto     = TributacaoProduto::where(["natureza_operacao_id"=>$natureza_operacao_id,"produto_id"=>$produto_id])->first();
        if($tributaProduto){
            $tributacao =  $tributaProduto->tributacao;
        }else{
            $tributacao = $tributacao_geral;
        }
        return $tributacao;
    }
}
