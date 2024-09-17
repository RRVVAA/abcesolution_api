<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nfe extends Model
{
    use HasFactory;
    protected $fillable =[
        'venda_id',
        'status_id',
        'natureza_operacao_id',
        'tipo_nfe_id',
        'nota_referencia_id',
        'chave',
        'recibo',
        'protocolo',
        'cUF',
        'cNF',
        'natOp',
        'modelo',
        'serie',
        'nNF',
        'cDV',
        'sequencia_cce',
        'dhEmi',
        'dhSaiEnt',
        'tpNF',
        'idDest',
        'cMunFG',
        'tpImp',
        'tpEmis',
        'tpAmb',
        'finNFe',
        'indFinal',
        'indPres',
        'indIntermed',
        'cnpjIntermed',
        'idCadIntTran',
        'tipo_nota_referenciada',
        'ref_NFe',
        'ref_ano_mes',
        'ref_num_nf',
        'ref_serie',
        'procEmi',
        'verProc',
        'dhCont',
        'xJust',
        'vBC',
        'vICMS',
        'vICMSDeson',
        'vFCP',
        'vBCST',
        'vST',
        'vFCPST',
        'vFCPSTRet',
        'vProd',
        'vFrete',
        'vSeg',
        'desconto_itens',
        'desconto_nota',
        'vDesc',
        'vII',
        'vIPI',
        'vIPIDevol',
        'vPIS',
        'vCOFINS',
        'vOutro',
        'vNF',
        'vTotTrib',
        'vOrig',
        'vLiq',
        'nFat',
        'em_xNome',
        'em_xFant',
        'em_IE',
        'em_IEST',
        'em_IM',
        'em_CNAE',
        'em_CRT',
        'em_CNPJ',
        'em_CPF',
        'em_xLgr',
        'em_nro',
        'em_xCpl',
        'em_xBairro',
        'em_cMun',
        'em_xMun',
        'em_UF',
        'em_CEP',
        'em_cPais',
        'em_xPais',
        'em_fone',
        'em_EMAIL',
        'em_SUFRAMA',
        'atualizacao',
        'modFrete',
        'tPag',
        'vPag',
        'CNPJ_pag',
        'tBand',
        'cAut',
        'tpIntegra',
        'indPag',
        'infAdFisco',
        'infCpl',
        'empresa_id',
        'resp_CNPJ',
        'resp_xContato',
        'resp_email',
        'resp_fone',
        'resp_CSRT',
        'resp_idCSRT',
        
        'transp_xNome',
        'transp_IE',
        'transp_xEnder',
        'transp_xMun',
        'transp_UF',
        'transp_CNPJ',
        'transp_placa',
        'transp_vagao',
        'transp_balsa',
        'transp_placa',
        'UF_placa',
        'transp_ret_vServ', 'transp_ret_vBCRet','transp_ret_pICMSRet','transp_ret_vICMSRet','transp_ret_CFOP','transp_ret_cMunFG',
        'transp_veic_placa','transp_veic_UF','transp_veic_RNTC',
        'transp_reboque_placa','transp_reboque_UF','transp_reboque_RNTC',
        'RNTC',
        'qVol',
        'esp',
        'marca',
        'nVol',
        'pesoL',
        'pesoB',
        'nLacre'
    ];
    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    public function venda(){
        return $this->belongsTo(Venda::class, 'venda_id');
    }
    
    public function status(){
        return $this->belongsTo(Status::class, 'status_id');
    }
        
    public function destinatario(){
        return $this->hasOne(NfeDestinatario::class, 'nfe_id');
    }
    
    public function transporte(){
        return $this->hasOne(NfeTransporte::class, 'nfe_id');
    }
    
    public function duplicatas(){
        return $this->hasMany(NfeDuplicata::class, 'nfe_id');
    }
    
    public function referenciados(){
        return $this->hasMany(NfeReferenciado::class, 'nfe_id');
    }
    
    public function autorizados(){
        return $this->hasMany(NfeAutorizado::class, 'nfe_id');
    }
    
    public function pagamentos(){
        return $this->hasMany(NfePagamento::class, 'nfe_id');
    }
    
    public function itens(){
        return $this->hasMany(NfeItem::class, 'nfe_id');
    }
}
