<?php
namespace App\Services;


use App\Models\Cobranca;
use App\Models\ItemOrcamento;
use App\Models\ItemPedidoCliente;
use App\Models\ItemVendaBalcao;
use App\Models\LojaItemPedido;
use App\Models\LojaPedido;
use App\Models\Orcamento;
use App\Models\OrdemServico;
use App\Models\PedidoCliente;
use App\Models\ProdutoOs;
use App\Models\ServicoOs;
use App\Models\VendaBalcao;
use App\Models\VendaRecorrente;
use App\Models\ItemVenda;
use App\Models\Venda;
use App\Models\Empresa;
use App\Models\PdvVenda;
use App\Models\LojaConfiguracao;

class ResgateService
{
         
    public static function lista($empresa_uuid){
        $empresa        = Empresa::where("uuid", $empresa_uuid)->first();
        $configuracao   = LojaConfiguracao::where("empresa_id",$empresa->id)->first();
        $vendaBalcao    = VendaBalcao::where(["empresa_id"=>$empresa->id, "status_id"=>config("constantes.status.FINALIZADO")])->get();
        $venda          = Venda::where("empresa_id",$empresa->id)->get();
        $pedidoLoja     = LojaPedido::where("empresa_id",$empresa->id)->get();
        $orcamento      = Orcamento::where("empresa_id",$empresa->id)->get();
        $os             = OrdemServico::where("empresa_id",$empresa->id)->get();
        $vendaRecorrente= VendaRecorrente::where("empresa_id",$empresa->id)->get();
        $pdvVenda       = PdvVenda::where(["empresa_id"=>$empresa->id, "status_id"=>config("constantes.status.PDVVENDA_SALVA")])->get();
        
        $retorno            = new \stdClass();
        $retorno->configuracao = $configuracao;
        $retorno->balcao    = $vendaBalcao;
        $retorno->loja      = $pedidoLoja;
        $retorno->orcamento = $orcamento;
        $retorno->os        = $os;
        $retorno->recorrente= $vendaRecorrente;
        $retorno->pdvVenda  = $pdvVenda;
        $retorno->venda     = $venda;
        return $retorno;
        
    }
    public static function buscaBalcao($codigo){
        $produtos = array();
        $lista = ItemVendaBalcao::where("venda_balcao_id",$codigo)->get();
        foreach($lista as $l){
            $retorno             = new \stdClass();
            $retorno->produto_id = $l->produto_id;
            $retorno->produto    = $l->produto->nome;
            $retorno->qtde       = $l->quantidade;
            $retorno->valor      = $l->valor;
            $retorno->subtotal   = $l->subtotal;
            $retorno->unidade    = $l->unidade;
            $produtos[]          = $retorno;
        }
        
        $venda = VendaBalcao::where("id",$codigo)->first();
        
        $v              = new \stdClass();
        $v->id          = $venda->id;
        $v->total       = $venda->valor_liquido;
        $v->data        = databr($venda->data_venda);
        $v->status_id   = $venda->status_id;
        
        return ["cabecalho" => $v, "produtos" =>$produtos];
    }
    
    public static function buscaLoja($codigo){
        $produtos = array();
        $lista = LojaItemPedido::where("pedido_id",$codigo)->get();
        foreach($lista as $l){
            $retorno             = new \stdClass();
            $retorno->produto_id = $l->produto_id;
            $retorno->produto    = $l->produto->nome;
            $retorno->qtde       = $l->quantidade;
            $retorno->valor      = $l->valor;
            $retorno->subtotal   = $l->subtotal;
            $retorno->unidade    = $l->unidade;
            $produtos[]          = $retorno;
        }
        
        $venda = LojaPedido::where("id",$codigo)->first();
        
        $v              = new \stdClass();
        $v->id          = $venda->id;
        $v->total       = $venda->valor_liquido;
        $v->data        = databr($venda->data_venda);
        $v->status_id   = $venda->status_id;
        
        return ["cabecalho" => $v, "produtos" =>$produtos];        
    }
    
    public static function buscaOrcamento($codigo){
        
        $produtos = array();
        $lista = ItemOrcamento::where("orcamento_id",$codigo)->get();
        foreach($lista as $l){
            $retorno             = new \stdClass();
            $retorno->produto_id = $l->produto_id;
            $retorno->produto    = $l->produto->nome;
            $retorno->qtde       = $l->quantidade;
            $retorno->valor      = $l->valor;
            $retorno->subtotal   = $l->subtotal;
            $retorno->unidade    = $l->unidade;
            $produtos[]          = $retorno;
        }
        
        $orcamento = Orcamento::where("id",$codigo)->first();
        
        $o              = new \stdClass();
        $o->id          = $orcamento->id; 
        $o->total       = $orcamento->valor_total;
        $o->data        = databr($orcamento->data_orcamento);
        $o->status_id   = $orcamento->status_id;
        
        return ["cabecalho" => $o, "produtos" =>$produtos];
    }
    
    public static function buscaOs($codigo){
        
        $produtos = array();
        $lista = ProdutoOs::where("os_id",$codigo)->get();
        foreach($lista as $l){
            $retorno             = new \stdClass();
            $retorno->produto_id = $l->produto_id;
            $retorno->produto    = $l->produto->nome;
            $retorno->qtde       = $l->quantidade;
            $retorno->valor      = $l->valor;
            $retorno->subtotal   = $l->subtotal;
            $retorno->unidade    = $l->unidade;
            $produtos[]          = $retorno;
        }
        
        $lista = ServicoOs::where("os_id",$codigo)->get();
        foreach($lista as $l){
            $retorno             = new \stdClass();
            $retorno->produto_id = $l->produto_id;
            $retorno->produto    = $l->produto->nome;
            $retorno->qtde       = $l->quantidade;
            $retorno->valor      = $l->valor;
            $retorno->subtotal   = $l->subtotal;
            $retorno->unidade    = $l->unidade;
            $produtos[]          = $retorno;
        }
        
        $os = OrdemServico::where("id",$codigo)->first();
        
        $o              = new \stdClass();
        $o->id          = $os->id;
        $o->total       = $os->valor_total;
        $o->data        = databr($os->data_orcamento);
        $o->status_id   = $os->status_id;
        
        return ["cabecalho" => $o, "produtos" =>$produtos];
    }
    
    
    public static function buscaCobranca($codigo){        
        $produtos = array();
        $cobranca = Cobranca::where("id",$codigo)->first();        
        return $cobranca;
        $retorno             = new \stdClass();
        $retorno->produto_id = $cobranca->produto_id;
        $retorno->produto    = $cobranca->produto->nome;
        $retorno->qtde       = $cobranca->quantidade;
        $retorno->valor      = $cobranca->valor;
        $retorno->subtotal   = $cobranca->subtotal;
        $retorno->unidade    = $cobranca->unidade;
        $produtos[]          = $retorno;
        
        $vendaRecorrente  = VendaRecorrente::find($cobranca->venda_recorrente_id);
        
        $o              = new \stdClass();
        $o->id          = $vendaRecorrente->id;
        $o->total       = $vendaRecorrente->valor_recorrente;
        $o->data        = databr($vendaRecorrente->data_contrato);
        $o->status_id   = $vendaRecorrente->status_id;
        
        return ["cabecalho" => $o, "produtos" =>$produtos];
    }
    
    public static function buscaVenda($codigo){
        $produtos = array();
        $lista = ItemVenda::where("venda_id",$codigo)->get();
        foreach($lista as $l){
            $retorno             = new \stdClass();
            $retorno->produto_id = $l->produto_id;
            $retorno->produto    = $l->produto->nome;
            $retorno->qtde       = $l->quantidade;
            $retorno->valor      = $l->valor;
            $retorno->subtotal   = $l->subtotal;
            $retorno->unidade    = $l->unidade;
            $produtos[]          = $retorno;
        }
        
        $venda = Venda::where("id",$codigo)->first();
        
        $v              = new \stdClass();
        $v->id          = $venda->id;
        $v->total       = $venda->valor_liquido;
        $v->data        = databr($venda->data_venda);
        $v->status_id   = $venda->status_id;
        
        return ["cabecalho" => $v, "produtos" =>$produtos];
    }
    
    public static function buscaPedido($codigo){
        $produtos = array();
        $lista = ItemPedidoCliente::where("pedido_id",$codigo)->get();
        foreach($lista as $l){
            $retorno = new \stdClass();
            $retorno->produto_id = $l->produto_id;
            $retorno->produto    = $l->produto->nome;
            $retorno->qtde      = $l->qtde;
            $retorno->valor      = $l->valor;
            $retorno->subtotal   = $l->subtotal;
            $retorno->unidade    = $l->produto->unidade;
            $produtos[]          = $retorno;
        }
        
        $pedido = PedidoCliente::where("id",$codigo)->first();
        
        $p              = new \stdClass();
        $p->id          = $pedido->id;
        $p->total       = $pedido->total;
        $p->data        = databr($pedido->data_pedido);
        $p->status_id   = $pedido->status_id;
        
        return ["cabecalho" => $p, "produtos" =>$produtos];
    }
    
    public static function busca($codigo){
        return $codigo;
    }
    
}

