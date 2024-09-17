<?php
namespace App\Services;


use App\Models\VendaBalcao;
use App\Models\LojaPedido;
use App\Models\Venda;
use App\Models\PdvVenda;
use App\Models\ItemVendaBalcao;
use App\Models\PdvItemVenda;
use App\Models\ItemOrcamento;
use App\Models\ItemVenda;
use App\Models\ItemPedidoCliente;
use App\Models\LojaItemPedido;
use App\Models\OrdemServico;
use App\Models\ProdutoOs;
use App\Models\ServicoOs;
use App\Models\User;
use App\Models\Cliente;

class ImportacaoParaVendaPdvService{
    
    public static function criarPdvVendaPelVenda($dados){
        $usuario =User::where("uuid", $dados->usuario_uuid)->first();
        $vendaNormal = Venda::find($dados->id);
        $venda = new \stdClass();
        $venda->venda_id        = $vendaNormal->id;
        $venda->empresa_id      = $dados->empresa_id;
        $venda->caixa_id        = $dados->caixa_id;
        $venda->usuario_id      = $usuario->id;
        $venda->vendedor_id     = $dados->vendedor_id;
        $venda->status_id       = config("constantes.status.DIGITACAO");
        $venda->data_venda      = hoje();
        $venda->estorno_estoque = 'N';
        $venda->valor_venda     = $vendaNormal->valor_venda;
        $venda->valor_frete     = $vendaNormal->valor_frete;
        $venda->desconto_valor  = $vendaNormal->desconto_valor;
        $venda->desconto_per    = $vendaNormal->desconto_per;
        $venda->valor_desconto  = $vendaNormal->valor_desconto;
        $venda->acrescimo_valor = 0;
        $venda->valor_liquido   = $vendaNormal->valor_liquido;
        $novaVenda = PdvVenda::Create(objToArray($venda));
        
        //itens
        $itens = ItemVenda::where("venda_id", $dados->id)->get();
        foreach($itens as $i){
            $item = new \stdClass();
            $item->venda_id             = $novaVenda->id;
            $item->produto_id           = $i->produto_id;
            $item->qtde                 = $i->quantidade;
            $item->valor                = $i->valor;
            $item->subtotal             = $i->subtotal;
            $item->subtotal_liquido     = $i->subtotal_liquido;
            $item->desconto_percentual  = $i->desconto_percentual;
            $item->desconto_por_valor   = $i->desconto_por_valor;
            $item->desconto_por_unidade = $i->desconto_por_unidade;
            $item->valor_desconto       = $i->valor_desconto;
            PdvItemVenda::Create(objToArray($item));
        }
        
        return $novaVenda;
    }
    
    public static function criarPdvVendaPelaLoja($dados){
        $usuario =User::where("uuid", $dados->usuario_uuid)->first();
        $loja = LojaPedido::find($dados->id);  
       
        $venda = new \stdClass();
        $venda->venda_loja_id   = $loja->id;
        $venda->empresa_id      = $dados->empresa_id;
        $venda->caixa_id        = $dados->caixa_id;
        $venda->usuario_id      = $usuario->id;
        $venda->status_id       = config("constantes.status.DIGITACAO");
        $venda->data_venda      = hoje();
        $venda->estorno_estoque = 'N';
        $venda->valor_venda     = $loja->valor_venda;
        $venda->valor_frete     = $loja->valor_frete;
        $venda->desconto_valor  = $loja->desconto_valor;
        $venda->desconto_per    = $loja->desconto_per;
        $venda->valor_desconto  = $loja->valor_desconto;
        $venda->acrescimo_valor = 0;
        $venda->valor_liquido   = $loja->valor_liquido;
        $novaVenda = PdvVenda::Create(objToArray($venda));
        
        //itens
        $itens = LojaItemPedido::where("pedido_id", $dados->id)->get();
        foreach($itens as $i){
            $item = new \stdClass();
            $item->venda_id             = $novaVenda->id;
            $item->produto_id           = $i->produto_id;
            $item->qtde                 = $i->quantidade;
            $item->valor                = $i->valor;
            $item->subtotal             = $i->subtotal;
            $item->subtotal_liquido     = $i->subtotal_liquido;
            $item->desconto_percentual  = $i->desconto_percentual;
            $item->desconto_por_valor   = $i->desconto_por_valor;
            $item->desconto_por_unidade = $i->desconto_por_unidade;
            $item->total_desconto_item  = $i->total_desconto_item;
            PdvItemVenda::Create(objToArray($item));
        }        
        return $novaVenda;        
    }
    
    public static function criarPdvVendaPeloOrcamento($dados){
        $usuario =User::where("uuid", $dados->usuario_uuid)->first();
        $orcamento = LojaPedido::find($dados->id);
        $venda = new \stdClass();
        $venda->orcamento_id    = $orcamento->id;
        $venda->empresa_id      = $dados->empresa_id;
        $venda->caixa_id        = $dados->caixa_id;
        $venda->usuario_id      = $usuario->id;
        $venda->vendedor_id     = $dados->vendedor_id;
        $venda->status_id       = config("constantes.status.DIGITACAO");
        $venda->data_venda      = hoje();
        $venda->estorno_estoque = 'N';
        $venda->valor_venda     = $orcamento->valor_venda;
        $venda->valor_frete     = $orcamento->valor_frete;
        $venda->desconto_valor  = $orcamento->desconto_valor;
        $venda->desconto_per    = $orcamento->desconto_per;
        $venda->valor_desconto  = $orcamento->valor_desconto;
        $venda->acrescimo_valor = 0;
        $venda->valor_liquido   = $orcamento->valor_liquido;
        $novaVenda = PdvVenda::Create(objToArray($venda));
        
        //itens
        $itens = ItemOrcamento::where("orcamento_id", $dados->id)->get();
        foreach($itens as $i){
            $item = new \stdClass();
            $item->venda_id             = $novaVenda->id;
            $item->produto_id           = $i->produto_id;
            $item->qtde                 = $i->quantidade;
            $item->valor                = $i->valor;
            $item->subtotal             = $i->subtotal;
            $item->subtotal_liquido     = $i->subtotal_liquido;
            $item->desconto_percentual  = $i->desconto_percentual;
            $item->desconto_por_valor   = $i->desconto_por_valor;
            $item->desconto_por_unidade = $i->desconto_por_unidade;
            $item->valor_desconto       = $i->valor_desconto;
            PdvItemVenda::Create(objToArray($item));
        }
        
        return $novaVenda;
        
    }
    
    public static function criarPdvVendaPeloBalcao($dados){
        $usuario    = User::where("uuid", $dados->usuario_uuid)->first();
        $balcao     = VendaBalcao::find($dados->id);
        $clientePadrao = Cliente::where(["empresa_id" => $balcao->empresa_id, "eh_consumidor"=>"S"])->first();
        $cliente_id = $balcao->cliente_id ? $balcao->cliente_id : $clientePadrao->id;
        $venda      = new \stdClass();
        $venda->venda_balcao_id  = $balcao->id;
        $venda->empresa_id      = $dados->empresa_id;
        $venda->caixa_id        = $dados->caixa_id;
        $venda->cliente_id      = $cliente_id;
        $venda->usuario_id      = $usuario->id;
        $venda->vendedor_id     = $dados->vendedor_id;
        $venda->status_id       = config("constantes.status.DIGITACAO");
        $venda->data_venda      = hoje();
        $venda->estorno_estoque = 'N';
        $venda->valor_venda     = $balcao->valor_venda;
        $venda->valor_frete     = $balcao->valor_frete;
        $venda->desconto_valor  = $balcao->desconto_valor;
        $venda->desconto_per    = $balcao->desconto_per;
        $venda->valor_desconto  = $balcao->valor_desconto;
        $venda->acrescimo_valor = 0;
        $venda->valor_liquido   = $balcao->valor_liquido;
        $novaVenda = PdvVenda::Create(objToArray($venda));
        
        //itens
        $itens = ItemVendaBalcao::where("venda_balcao_id", $dados->id)->get();
        foreach($itens as $i){
            $item = new \stdClass();
            $item->venda_id      = $novaVenda->id;
            $item->produto_id           = $i->produto_id;
            $item->qtde                 = $i->quantidade;
            $item->valor                = $i->valor;
            $item->subtotal             = $i->subtotal;
            $item->subtotal_liquido     = $i->subtotal_liquido;
            $item->desconto_percentual  = $i->desconto_percentual;
            $item->desconto_por_valor   = $i->desconto_por_valor;
            $item->desconto_por_unidade = $i->desconto_por_unidade;
            $item->valor_desconto       = $i->valor_desconto;
            PdvItemVenda::Create(objToArray($item));
            
        }
        
        $balcao->status_id = config("constantes.status.CONCRETIZADO");
        $balcao->save();
        return $novaVenda;
       
    }
    
    public static function criarPdvVendaPelOrdemServico($dados){
        $usuario =User::where("uuid", $dados->usuario_uuid)->first();
        $os = OrdemServico::find($dados->id);
        $venda = new \stdClass();
        $venda->os_id           = $os->id;
        $venda->empresa_id      = $dados->empresa_id;
        $venda->caixa_id        = $dados->caixa_id;
        $venda->usuario_id      = $usuario->id;
        $venda->vendedor_id     = $dados->vendedor_id;
        $venda->status_id       = config("constantes.status.DIGITACAO");
        $venda->data_venda      = hoje();
        $venda->estorno_estoque = 'N';
        $venda->valor_venda     = $os->valor_os;
        $venda->valor_frete     = $os->valor_frete;
        $venda->desconto_valor  = $os->desconto_valor;
        $venda->desconto_per    = $os->desconto_per;
        $venda->valor_desconto  = $os->valor_desconto;
        $venda->acrescimo_valor = 0;
        $venda->valor_liquido   = $os->valor_liquido;
        $novaVenda = PdvVenda::Create(objToArray($venda));
        
        //itens
        $itens = ProdutoOs::where("os_id", $dados->id)->get();
        foreach($itens as $i){
            $item = new \stdClass();
            $item->venda_id             = $novaVenda->id;
            $item->produto_id           = $i->produto_id;
            $item->qtde                 = $i->quantidade;
            $item->valor                = $i->valor;
            $item->subtotal             = $i->subtotal;
            $item->subtotal_liquido     = $i->subtotal_liquido;
            $item->desconto_percentual  = $i->desconto_percentual;
            $item->desconto_por_valor   = $i->desconto_por_valor;
            $item->desconto_por_unidade = $i->desconto_por_unidade;
            $item->valor_desconto       = $i->valor_desconto;
            PdvItemVenda::Create(objToArray($item));
            
        }
        
        //itens
        $servicos = ServicoOs::where("os_id", $dados->id)->get();
        foreach($servicos as $i){
            $item = new \stdClass();
            $item->venda_id             = $novaVenda->id;
            $item->produto_id           = $i->produto_id;
            $item->qtde                 = $i->quantidade;
            $item->valor                = $i->valor;
            $item->subtotal             = $i->subtotal;
            $item->subtotal_liquido     = $i->subtotal_liquido;
            $item->desconto_percentual  = $i->desconto_percentual;
            $item->desconto_por_valor   = $i->desconto_por_valor;
            $item->desconto_por_unidade = $i->desconto_por_unidade;
            $item->valor_desconto       = $i->valor_desconto;
            PdvItemVenda::Create(objToArray($item));            
        }
        
        return $novaVenda;
    }
    
    public static function criarPdvVendaPeloPedidoCliente($dados){
        $usuario =User::where("uuid", $dados->usuario_uuid)->first();
        $pedido = Venda::find($dados->id);
        $venda = new \stdClass();
        $venda->pedido_cliente_id= $pedido->id;
        $venda->empresa_id      = $dados->empresa_id;
        $venda->caixa_id        = $dados->caixa_id;
        $venda->usuario_id      = $usuario->id;
        $venda->status_id       = config("constantes.status.DIGITACAO");
        $venda->data_venda      = hoje();
        $venda->estorno_estoque = 'N';
        $venda->valor_venda     = $pedido->total;
        $venda->valor_frete     = 0;
        $venda->desconto_valor  = 0;
        $venda->desconto_per    = 0;
        $venda->valor_desconto  = 0;
        $venda->acrescimo_valor = 0;
        $venda->valor_liquido   = 0;
        $novaVenda = PdvVenda::Create(objToArray($venda));
        
        //itens
        $itens = ItemPedidoCliente::where()->get();
        foreach($itens as $i){
            $item = new \stdClass();
            $item->venda_id             = $novaVenda->id;
            $item->produto_id           = $i->produto_id;
            $item->qtde                 = $i->quantidade;
            $item->valor                = $i->valor;
            $item->subtotal             = $i->subtotal;
            $item->subtotal_liquido     = $i->subtotal;
            $item->desconto_percentual  = 0;
            $item->desconto_por_valor   = 0;
            $item->desconto_por_unidade = 0;
            $item->valor_desconto       = 0;
            ItemPedidoCliente::Create($item);
        }
        
        return $novaVenda;
    }
    
   
}

