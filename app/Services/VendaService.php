<?php
namespace App\Services;

use App\Models\Cliente;
use App\Models\FinContaReceber;
use App\Models\FinRecebimento;
use App\Models\ItemVenda;
use App\Models\LojaPedido;
use App\Models\Venda;
use Illuminate\Support\Str;

class VendaService{
    
    public static function gerarVendaDaLojaPeloPedido($id){
        $pedido                 = LojaPedido::find($id);       
        
        $lojaCliente            = $pedido->cliente;
        $lojaEndereco           = $pedido->endereco;
        
        $temCliente             = Cliente::where("email", $lojaCliente->email)->first();
        //Verifica se o cliente tá cadastrado, se não cadastra
        if(!$temCliente){
            $cli                    = new \stdClass();
            $cli->empresa_id        = $pedido->empresa_id;
            $cli->tipo_cliente      = "F";
            $cli->nome_razao_social = $lojaCliente->nome ;
            $cli->nome_fantasia     = $lojaCliente->nome ;
            $cli->tipo_contribuinte = 9 ;
            $cli->indFinal          = 1 ;
            $cli->uuid              = Str::uuid();
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
        
        //verifica se tem venda
        $temVenda = Venda::where("pedido_loja_id", $pedido->id)->first();       
        
        //Dados para a venda
        if(!$temVenda){
            $ven                    = new \stdClass();
            $ven->empresa_id        = $pedido->empresa_id;
            $ven->cliente_id        = $cliente->id;
            $ven->usuario_id        = 1;
            $ven->forma_pagamento   = "a_vista";
            $ven->forma_pagto_id   	= $pedido->forma_pagto_id;
            $ven->tPag              = config("constantes.forma_pagto.PIX");
            $ven->pedido_loja_id    = $pedido->id;
            $ven->valor_total       = $pedido->valor_total - $pedido->valor_frete;
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
            $ven->status_financeiro_id = config("constantes.status.PAGO");
            $venda                  = Venda::Create(objToArray($ven));                
           
            if($venda){
                //Criar os itens da venda
                foreach($pedido->itens as $i){                
                    $it = new \stdClass();
                    $it->venda_id   = $venda->id;
                    $it->produto_id = $i->produto_id;
                    $it->quantidade = $i->quantidade;
                    $it->valor      = $i->valor;
                    $it->unidade    = $i->produto->unidade;
                    $it->subtotal   = $i->quantidade * $i->valor;
                    $temItem        = ItemVenda::where(["venda_id" =>$venda->id, "produto_id" =>$i->produto_id])->first();
                    if(!$temItem){
                        ItemVenda::Create(objToArray($it));
                    }
                    
                }
                
                $pedido->venda_id   = $venda->id;
                $pedido->status_id  = config("constantes.status.FINALIZADO");;
                $pedido->save();
            
                //Inserir conta a receber 
                
                $novaContaReceber = new \stdClass();
                $novaContaReceber->empresa_id		= $pedido->empresa_id;
                $novaContaReceber->cliente_id		= $cliente->id;
                $novaContaReceber->venda_id			= $venda->id;
                $novaContaReceber->num_parcela		= 1;
                $novaContaReceber->ult_parcela		= 1;
                $novaContaReceber->data_emissao		= hoje();
                $novaContaReceber->data_vencimento	= hoje();
                $novaContaReceber->descricao	    ="Venda Loja virtual #".$venda->id ;
                $novaContaReceber->valor	        = $venda->valor_venda;
                $novaContaReceber->status_id        = config("constantes.status.PAGO");
                
                $temContaReceber = FinContaReceber::where(["venda_id"=>$venda->id])->first();
                if(!$temContaReceber){
                    $contaReceber =  FinContaReceber::Create(objToArray($novaContaReceber));
                    
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
                $itens = ItemVenda::where("venda_id", $venda->id)->get();
                foreach($itens as $item){
                    $mov                    = new \stdClass();
                    $mov->tipo_movimento_id = 14;
                    $mov->empresa_id	    = $pedido->empresa_id;
                    $mov->produto_id        = $item->produto_id;
                    $mov->ent_sai           = 'S';
                    $mov->estorno           = 'N';
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
        }else{
            $venda = $temVenda;
        }
        return $venda;
    }
    
    public static function dadosVenda($dados){
        $dados = (object) $dados;
        //i($dados);
        //Verifica se existe
        if(!isset($dados->empresa_id)){
            return "Campo " . "Empresa " . "Não Existe!" ;
        }
        if(!isset($dados->caixa_id)){
            return "Campo " . "Caixa  " . "Não Existe!" ;
        }
        if(!isset($dados->empresa_id)){
            return "Campo " . "Empresa " . "Não Existe!" ;
        }
        if(!isset($dados->usuario_id)){
            return "Campo " . "Usuário " . "Não Existe!" ;
        }
        
        if(!isset($dados->valor_total)){
            return "Campo " . "Valor Tatal " . "Não Existe!" ;
        }
        
        if(!isset($dados->total_receber)){
            return "Campo " . "Total a Receber " . "Não Existe!" ;
        }
        
        if(!isset($dados->valor_desconto)){
            return "Campo " . "Valor desconto " . "Não Existe!" ;
        }        
        
        // Valores do 
        if($dados->empresa_id==""){
            return   "O Código da Empresa é obrigatório";
        }
        
        if($dados->caixa_id==""){
            return   "O Código do Caixa é obrigatório";
        }
        
        if($dados->usuario_id==""){
            return   "O Código do Usuário é obrigatório";
        }
        
        if($dados->valor_total<=0){
            return   "O Valor total precisa ser maior que zero";
        }
        
        if($dados->total_receber<=0){
            return   "O total a receber precisa ser maior que zero";
        }       
        
    }
    public static function dadosItemVenda($itens){
        foreach($itens as $item){
            $item = (object) $item;
            if(!isset($item->codigo)){
                return "Campo " . "codigo " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->nome)){
                return "Campo " . "nome " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->quantidade)){
                return "Campo " . "quantidade " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->valor)){
                return "Campo " . "valor " . "Não Existe!" ;
                break;
            }
            
            if($item->codigo==""){
                return "O Código do Produto é Obrigatório";
                break;
            }
            
            if($item->nome==""){
                return "O Campo Nome do Produto é Obrigatório";
                break;
            }
            
            if($item->quantidade==""){
                return "O Campo Quantidade é Obrigatório";
                break;
            }
            
            if($item->valor <=0){
                return "O Campo Valor precisa ser maior que Zero";
                break;
            }
        }
    }
}

