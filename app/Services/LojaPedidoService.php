<?php
namespace App\Services;

use App\Models\LojaCliente;
use App\Models\LojaEnderecoCliente;
use App\Models\LojaItemPedido;
use App\Models\LojaPedido;

class LojaPedidoService{
    public function finalizar($pedido_id){
       // $pedido = LojaPedido::where("id", $req["id"])->update($req);
        return VendaService::gerarVendaDaLojaPeloPedido($pedido_id);
    }
    
    public function criarNovoPedido($dados, $empresa){
        $dados = (object) $dados; 
        $cli   = (object) $dados->cliente; 
        $end   = (object) $dados->endereco;
        $itens = (object) $dados->itens;
       
        //Cliente
        $c = new \stdClass();
        $c->id          = $cli->id; 
        $c->empresa_id  = $empresa->id;
        $c->nome        = $cli->nome ;
        $c->cpf         = $cli->cpf ;
        $c->email       = $cli->email ;
        $c->telefone    = $cli->telefone ;
        $c->senha       = $cli->senha ;
        $c->password    = $cli->password ;
        $c->status_id   = config('constantes.status.ATIVO'); 
        $cliente = LojaCliente::find($cli->id);
        if(!$cliente){
            $cliente   = LojaCliente::Create(objToArray($c));
        }else{
            $cliente->update(objToArray($cli));
        }
        
       
        //Endereco
        $e              = new \stdClass();
        $e->id          = $end->id;
        $e->cliente_id  = $end->cliente_id ;
        $e->empresa_id  = $empresa->id;
        $e->rua         = $end->rua ;
        $e->numero      = $end->numero ;
        $e->bairro      = $end->bairro ;
        $e->cidade      = $end->cidade ;
        $e->cep         = $end->cep ;
        $e->uf          = $end->uf ;
        $e->complemento = $end->complemento ;        
        $endereco = LojaEnderecoCliente::find($end->id);
        if(!$endereco){
            $endereco   = LojaEnderecoCliente::Create(objToArray($e));
        }else{
            $endereco->update(objToArray($e));
        }
                
        //Pedido
        $p = new \stdClass();
        $p->id            = $dados->id;
        $p->cliente_id    = $dados->cliente_id;
        $p->empresa_id    = $empresa->id;
        $p->endereco_id   = $dados->endereco_id;
        $p->status_id     = config('constantes.status.PENDENTE');
        $p->valor_total   = $dados->valor_total;
        $p->valor_frete   = $dados->valor_frete;
        $p->tipo_frete    = $dados->tipo_frete;
        $p->venda_id      = $dados->venda_id;
        $p->numero_nfe    = $dados->numero_nfe;
        $p->observacao    = $dados->observacao;
        $pedido = LojaPedido::find($dados->id);
        if(!$pedido){
            $pedido   = LojaPedido::Create(objToArray($p));
        }else{
            $pedido->update(objToArray($p));
        }
     
        //Itens
         foreach($itens as $it){
            $it = (object) $it; 
            $i              = new \stdClass();
            $i->id          = $it->id;
            $i->pedido_id   = $it->pedido_id;
            $i->produto_id  = $it->produto_id;
            $i->quantidade  = $it->qtde;
            $i->valor       = $it->valor;
            $i->subtotal    = $it->subtotal;
            $item = LojaItemPedido::find($it->id);            
            if(!$item){
                $item   = LojaItemPedido::Create(objToArray($i));
            }else{
                $item->update(objToArray($i));
            }
        }             
       
        return $pedido;
    }
}

