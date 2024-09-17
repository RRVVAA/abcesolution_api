<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TributacaoEstado extends Model
{
    use HasFactory;
    protected $fillable = [
        'natureza_operacao_id',
        'estado_id',
        'tributacao_id',
        'tributacao_contribuinte_id',
        'cst',
        'pICMS',
        'pFCP',        
        'cfop',
    ];
    
    public function estado(){
        return $this->belongsTo(Estado::class,"estado_id");
    }
    
    public function tipoContribuinte(){
        return $this->belongsTo(TipoContribuinte::class,"tributacao_contribuinte_id");
    }
    
    public function tributacao(){
        return $this->belongsTo(Tributacao::class,"tributacao_id");
    }
    
    public static function listaPorTributacao($tributacao_id){
        $lista = TributacaoEstado::where("tributacao_id", $tributacao_id)->get();     
        $resultado = array();
        if($lista){
            foreach ($lista as $l){              
                $t                  = new \stdClass();
                $t->id              = $l->id;
                $t->estado_id       = $l->estado_id ;
                $t->estado          = $l->estado->estado ;
                $t->tributacao_id   = $l->tributacao_id;
                $t->natureza_operacao_id = $l->natureza_operacao_id;
                $t->tributacao_contribuinte_id  = $l->tributacao_contribuinte_id ;                
                $t->tipo_contribuinte = $l->tipoContribuinte->tipo_contribuinte;   
                $t->cst             = $l->cst;
                $t->cfop             = $l->cfop;
                $t->pICMS           = $l->pICMS;
                $t->pFCP            = $l->pFCP;
                $resultado[]        = $t;
            }
        }
        
        return (object) $resultado;
    }
}
