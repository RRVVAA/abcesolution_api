<?php

namespace App\Models;

use App\Service\ConstanteService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NfePagamento extends Model
{
    use HasFactory;
    protected $fillable = [
        'nfe_id',
        'tPag',
        'vPag',
        'CNPJ',
        'tBand',
        'cAut',
        'tpIntegra',
        'indPag'
    ];
    
    public static function lista($nfe_id){
        $lista = NfePagamento::where("nfe_id", $nfe_id)->get();
        $l = array();
        if($lista){
            foreach($lista as $p){
                $pag = new \stdClass();
                $pag->id        = $p->id;
                $pag->tPag      = $p->tPag;
                $pag->pagamento = $p->tPag ? ConstanteService::getTipoPagamento($p->tPag) : $p->tPag;
                $pag->vPag      = $p->vPag;
                $pag->CNPJ      = $p->CNPJ;
                $pag->tBand     = $p->tBand;
                $pag->cAut      = $p->cAut;
                $pag->tpIntegra = $p->tpIntegra;
                $pag->indPag    = $p->indPag;
                $pag->tipo_pagto= ($p->indPag== 0) ? "à Vista" : "à Prazo" ;
                $pag->vTroco    = $p->vTroco;
                $l[] = $pag;
            }
        }
        
      return (object) $l;
    }
}
