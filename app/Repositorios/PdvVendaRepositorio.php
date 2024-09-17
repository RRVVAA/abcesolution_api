<?php
namespace App\Repositorios;

use App\Models\PdvVenda;
use App\Repositorios\Contratos\PdvVendaRepositorioInterface;
use Illuminate\Support\Facades\DB;

class PdvVendaRepositorio implements PdvVendaRepositorioInterface
{
    protected $entidade;
    
    
    public function __construct(PdvVenda $pdvVenda){
        $this->entidade = $pdvVenda;  
    }
    
    public function getVendaAbertaPorUsuario($usuario_id, $caixa_id){
        return $this->entidade->where("usuario_id", $usuario_id)
                              ->where("caixa_id", $caixa_id)
                              ->where("status_id", config("constantes.status.DIGITACAO"))
                              ->first();
    }       
   
    
    public function getVendaPorId($id_venda){
        return $this->entidade->where("id", $id_venda)->first();
    }
    
    public function salvar($venda){        
        //Calcula o total
        $total = DB::table('pdv_item_vendas')->where("venda_id", $venda->venda_id)->sum("subtotal");
        $v = [
            "cliente_nome"  => $venda->cliente_nome,
            "cliente_cpf"   => $venda->cliente_cpf,
            "valor_total"   => $total,
            "valor_desconto"=> $venda->valor_desconto,
            "valor_liquido" =>$total -  $venda->valor_desconto,
            "status_id"     =>config("constantes.status.CONCRETIZADO")
        ];      
        
        $this->entidade->where("id", $venda->venda_id)->update($v);
        
    }
    
    public function novaVenda($usuario_id, $caixa_id, $empresa_id )
    {
        $v                  = new \stdClass();
        $v->usuario_id      = $usuario_id;
        $v->caixa_id        = $caixa_id;
        $v->empresa_id      = $empresa_id ;
        $v->valor_total     = 0; 
        $v->valor_desconto  = 0; 
        $v->desconto_per    = 0; 
        $v->acrescimo_valor = 0;
        $v->acrescimo_per   = 0;
        $v->valor_acrescimo = 0;
        $v->valor_liquido   = 0;
        $v->data_venda      = hoje();
        $v->status_id       = config("constantes.status.DIGITACAO");     
        
        return $this->entidade->create(objToArray($v));
    }
    
    public function inserirItensVenda(int $id_venda, $itens)
    {       
		$itensVenda = [];
        foreach ($itens as $item) {          
            array_push($itensVenda, [
                'venda_id'      	=> $id_venda,
                "produto_id"    	=> $item["codigo"],
                'qtde'          	=> $item["quantidade"],
                'valor'         	=> $item["valor"],
                'desconto_item' 	=> $item["desconto_item"],
                'subtotal'      	=> $item["valor"]  * $item["quantidade"],
				'subtotal_liquido'  => ($item["valor"] - $item["desconto_item"]) * $item["quantidade"]
            ]);         
            
        }       
        DB::table('pdv_item_vendas')->insert($itensVenda);
    }

    public function inserirPagamentos(int $id_venda, $id_caixa, $pagamentos)
    {
        $itensPagamentos = [];
        foreach ($pagamentos as $pag) {
            
            array_push($itensPagamentos, [
                'venda_id'          => $id_venda,
                "caixa_id"          => $id_caixa,
                'forma_pagto_id'   => $pag["forma_pagto_id"],
                'num_parcela'       => $pag["qtde_vezes"],
                'valor'             => $pag["valor"] ,
                'subtotal'          => $pag["valor"] * $pag["qtde_vezes"]
            ]);
            
        }        
        
        DB::table('pdv_pagamentos')->insert($itensPagamentos);
    }
	
    public function listaPorCaixa($caixa_id){
        return $this->entidade->where("caixa_id", $caixa_id)->get();
    }
    
    public function listaPorUsuario($usuario_id){
        return $this->entidade->where("usuario_id", $usuario_id)->get();
    }


}

