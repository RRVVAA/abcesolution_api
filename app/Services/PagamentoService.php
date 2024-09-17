<?php
namespace App\Services;

use App\Models\Cliente;
use App\Models\FinContaReceber;
use App\Models\FinRecebimento;
use App\Models\ItemVenda;
use App\Models\LojaPedido;
use App\Models\Parametro;
use App\Models\Venda;

class PagamentoService{

    public static function gerarVenda($id){        
        $pedido                 = LojaPedido::find($id); 
       
        $lojaCliente            = $pedido->cliente;
        $lojaEndereco           = $pedido->endereco;
        
        $temCliente             = Cliente::where("cpf_cnpj", $lojaCliente->cpf)->first();  
        $parametro              = Parametro::where("empresa_id", $pedido->empresa->id)->first();
        //Verifica se o cliente tá cadastrado, se não cadastra
        if(!$temCliente){
            $cli                    = new \stdClass();
            $cli->empresa_id        = $pedido->empresa_id;
            $cli->tipo_cliente      = "F";
            $cli->nome_razao_social = $lojaCliente->nome ;
            $cli->nome_fantasia     = $lojaCliente->nome ;
            $cli->tipo_contribuinte = 9 ;
            $cli->cpf_cnpj          = $lojaCliente->cpf;
            $cli->logradouro        = $lojaEndereco->rua;
            $cli->senha             = $lojaCliente->senha;
            $cli->numero            = $lojaEndereco->numero;
            $cli->bairro            = $lojaEndereco->bairro;
            $cli->uf                = $lojaEndereco->uf;
            $cli->ibge              = $lojaEndereco->ibge;
            $cli->complemento       = $lojaEndereco->complemento;
            $cli->telefone          = $lojaCliente->telefone;
            $cli->celular           = $lojaEndereco->telefone;
            $cli->email             = $lojaCliente->email;
            $cli->cep               = $lojaEndereco->cep;
            $cli->cidade            = $lojaEndereco->cidade;
            $cli->status_id         = $lojaCliente->status_id;
            $cli->password          = $lojaCliente->password;
            $cli->status_id         = config("constantes.status.ATIVO");
            $cliente                = Cliente::Create(objToArray($cli));
        }else{
            $cliente = $temCliente;
        }
     
       
        //Dados para a venda
        $ven                    = new \stdClass();
        $ven->empresa_id        = $pedido->empresa_id;
        $ven->cliente_id        = $cliente->id;
        $ven->usuario_id        = 1;        
        $ven->forma_pagamento   = "a_vista";
        $ven->forma_pagto_id   	= $pedido->forma_pagto_id;
        $ven->tPag              = $pedido->forma_pagamento->cod;
        
        if($ven->tPag=="03" || $ven->tPag=="04"  ){
            /*$ven->bandeira_cartao   = ($venda['bandeira_cartao']) ?? NULL;
            $ven->cnpj_cartao       = ($venda['cnpj_cartao']) ?? NULL;
            $ven->cAut_cartao       = ($venda['cAut_cartao']) ?? NULL;*/
        }
        
		$ven->pedido_loja_id    = $pedido->id;
        $ven->valor_total       = $pedido->valor_total;
        $ven->valor_venda       = $pedido->valor_total;
        $ven->valor_desconto    = 0;
        $ven->valor_frete       = $pedido->valor_frete;
        $ven->qtde_parcela      = 1;
        $ven->data_venda        = hoje();
        $ven->primeiro_vencimento= hoje();
        $ven->xml_path          = '';
        $ven->chave             = '';
        $ven->numero_emissao    = 0;
        $ven->status_id         = config("constantes.status.PAGO");
        $venda                  = Venda::Create(objToArray($ven));      
        
        
        if($venda){
                //Criar os itens da venda
            foreach($pedido->itens as $i){
                $it = new \stdClass();
                $it->venda_id   = $venda->id;
                $it->produto_id = $i->produto_id;
                $it->quantidade = $i->quantidade;
                $it->valor      = $i->valor;
                $it->subtotal   = $i->quantidade * $i->valor;
                ItemVenda::Create(objToArray($it));
            }
                
            $pedido->venda_id           = $venda->id;
            $pedido->save();            
            
            //Inserir conta a receber 
            if($parametro->lancar_financeiro_lojavirtual=="S"){
               $contaReceber = FinContaReceber::create([
                   "empresa_id"		    => $pedido->empresa_id,
                   "cliente_id"		    => $cliente->id,
                   "venda_id"			=> $venda->id,
                   "num_parcela"		=> 1,
                   "ult_parcela"		=> 1,
                   "data_emissao"		=> hoje(),
                   "data_vencimento"	=> hoje(),
                   "descricao"	        =>"Venda Loja virtual #".$venda->id ,
                   "valor"	            => $venda->valor_venda ,
                   "status_id"         => config("constantes.status.PAGO")
               ]);
                
               //Criar um recebimento
               $receb = new \stdClass();
               $receb->empresa_id              = $pedido->empresa_id;
               $receb->usuario_id              = 1;
               $receb->descricao_recebimento   = "Conta a Receber #" .$contaReceber->id;
               $receb->forma_pagto_id          = $pedido->forma_pagto_id ;
               $receb->data_recebimento        = hoje();
               $receb->numero_documento        = $pedido->transacao_id;
               $receb->observacao              = "Pagamento da Loja Virtual";
               $receb->valor_original          = $venda->valor_venda;
               $receb->juros                   = 0;
               $receb->desconto                = 0;
               $receb->multa                   = 0;
               $receb->valor_recebido          = $venda->valor_venda;
               $recebimento = FinRecebimento::Create(objToArray($receb));
               
               $contaReceber->recebimento_id = $recebimento->id;
               $contaReceber->save();
            }           
            
           //Lançando no Estoque
            if($parametro->lancar_estoque_lojavirtual=="S"){
                $itens = ItemVenda::where("venda_id", $venda->id)->get();
               foreach($itens as $item){
                   $mov                    = new \stdClass();
                   $mov->tipo_movimento_id = 14;
                   $mov->empresa_id	       = $pedido->empresa_id;
                   $mov->produto_id        = $item->produto_id;
                   $mov->ent_sai           = 'S';
                   $mov->data_movimento    = hoje();
                   $mov->qtde_movimento    = $item->quantidade;
                   $mov->valor_movimento   = $item->valor;
                   $mov->subtotal_movimento= $item->subtotal;
                   $mov->descricao         = "Venda Loja Virtual#" . $venda->id;
                   if($item->produto->controlar_estoque=="S"){
                       MovimentoService::inserir($mov);
                   }
               }
           }
           
        }
    }
}

