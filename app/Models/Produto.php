<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Produto extends Model
{    
    protected $fillable =[
        'empresa_id',
        'nome',
        'gtin',
        'imagem',
        'uuid',
        'tributacao_id',
        'categoria_id',
        'unidade',
        'referencia',
        'valor_venda',
        'valor_custo',
        'custo_medio',
        'estoque_minimo',
        'estoque_maximo',
        'estoque_inicial',
        'estoque_atual',
        'cfop',
        'ncm',
        'cest',
        'cbenef',
        'tipi',
        'unidade_tributavel',
        'quantidade_tributavel',
        'produto_loja', 'descricao', 'controlar_estoque', 'status_id',
        'preco', 'destaque', 'cep', 'largura','comprimento','altura','peso_liquido','peso_bruto'
        ];
    
  
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    
    public function estoque(){
        return $this->hasOne(Estoque::class, 'produto_id', 'id');
    }
    public function tributacao(){
        return $this->belongsTo(Tributacao::class, 'tributacao_id');
    }
    
    public function categoria(){
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
    

    public static function atualizarEstoque($produto_id, $qtde){
        $sql = "UPDATE produto SET
                        estoque_atual = estoque_atual + ($qtde) ,
                        estoque_real  = estoque_real + ($qtde)
                WHERE
                        id = $produto_id";
        DB::update($sql);
    }
    
    public static function reservarEstoque($produto_id, $qtde){
        $sql = "UPDATE produto SET
                        estoque_atual = estoque_atual - ($qtde) ,
                        estoque_reservado  = estoque_reservado + ($qtde)
                WHERE
                        id = $produto_id";
        DB::update($sql);
    }
    
    public static function excluirReservaDeEstoque($produto_id, $qtde){
        $sql = "UPDATE produto SET
                estoque_real        = estoque_real       - ($qtde),
                estoque_reservado   = estoque_reservado  - ($qtde)
                WHERE id    = $produto_id
            ";
        DB::update($sql);
    }
    
    public static function verificaCadastrado($ean, $nome, $referencia){
        $result = null;
        
        $result = Produto:: where('referencia', $referencia)->first();        
        if(!$result){
            $result = Produto::where('nome', $nome)->first();
        }
        
        if(!$result){
            $result = Produto::where('gtin', $ean)->where('gtin', '!=', 'SEM GTIN')->first();
        }        
        return $result;
    }
    
   
    
}
