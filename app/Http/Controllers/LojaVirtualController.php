<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\LojaBanner;
use App\Models\LojaCliente;
use App\Models\LojaConfiguracao;
use App\Models\LojaEnderecoCliente;
use App\Models\LojaItemPedido;
use App\Models\LojaPedido;
use App\Models\Produto;
use App\Services\PagamentoService;
use Illuminate\Http\Request;
use App\Services\LojaClienteService;

class LojaVirtualController extends Controller{
    public function listaProduto($id_empresa){        
        $lista = Produto::where(["empresa_id"=>$id_empresa,"produto_loja"=>'S'])->get();
        echo json_encode($lista);
    }
    
    public function getProduto($id_produto){
        $produto = Produto::where(["id"=>$id_produto,"produto_loja"=>'S'])->first();
        echo json_encode($produto);
    }
    
    public function getProdutosSemelhantes($id_categoria, $id_empresa){
        $produtos = Produto::where(["categoria_id"=>$id_categoria,"empresa_id"=>$id_empresa])->get();
        echo json_encode($produtos);
    }
     
    public function getCliente($id_cliente){
        $cliente = LojaCliente::where(["id"=>$id_cliente])->with('enderecos')->with('pedidos')->first();
        echo json_encode($cliente);
    }
    
    public function getConfiguracao($id_empresa){
        $lista = LojaConfiguracao::where(["empresa_id"=>$id_empresa])->first();
        echo json_encode($lista);
    }
    
    public function listaBanner($id_empresa){
        $lista = LojaBanner::where(["empresa_id"=>$id_empresa,"status_id"=>config("constantes.status.ATIVO")])->get();
        echo json_encode($lista);
    }
    
    public function listaCategoria($id_empresa){
        $lista = Categoria::where(["empresa_id"=>$id_empresa])->get();
        echo json_encode($lista);
    }
    
    public function getEnderecoCliente($id_endereco){
        $endereco = LojaEnderecoCliente::where(["id"=>$id_endereco])->first();
        echo json_encode($endereco);
    }
    
    public function excluirItemCarrinho($id_item){
        $item = LojaItemPedido::where('id', $id_item)->first();
        $item->delete();
        echo json_encode($item);
    }
    
    public function getPedidoAleatorio($rand){
        $pedido = LojaPedido::where('rand_pedido', $rand)
                                ->where('status_id', config('constantes.status.DIGITACAO'))
                                ->with('cliente')
                                ->with('cliente.enderecos')
                                ->with("itens")
                                ->with("itens.produto")
                                ->first();
        echo json_encode($pedido);
    }
    
    public function getPedidoNovoDoCliente($id_cliente){
        $pedido = LojaPedido::where('cliente_id', $id_cliente)
                              ->where('status_id', config('constantes.status.DIGITACAO'))
                              ->with('cliente')
                              ->with('cliente.enderecos')
                              ->with("itens")
                              ->with("itens.produto")
                              ->first();
        echo json_encode($pedido);
    }
    
    public function getPedidoPorPedidoCliente($id_pedido, $id_cliente){
        $pedido = LojaPedido::where('id', $id_pedido)
                            ->where('cliente_id', $id_cliente)
                            ->with('cliente')
                            ->with('cliente.enderecos')
                            ->with("itens")
                            ->with("itens.produto")
                            ->first();
        echo json_encode($pedido);
    }
    
    public function fazerLogin(Request $request){        
        try{
            $cliente = LojaCliente::where(['email'=>$request->email,'senha'=> $request->senha, "empresa_id"=>$request->empresa_id])->first();
           
            if ($cliente){
                if($request->rand){
                    LojaPedido::where('rand_pedido', $request->rand)->update(["cliente_id" => $cliente->id]);
                }
            }
            echo json_encode($cliente);            
        } catch (\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
    
    public function novoPedido(Request $request){
        $req = $request->all();
        try{
            $pedido = LojaPedido::create($req);
            echo json_encode($pedido);
            
        } catch (\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
    
	public function pagarPedido(Request $request){
        $req = $request->all();
        try{
            $pedido = LojaPedido::where("id", $req["id"])->update($req);
            PagamentoService::gerarVenda($req["id"]);
            echo json_encode($pedido);
        } catch (\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
	
    public function atualizarPedido(Request $request){
        $req = $request->all();
       
        try{
            $pedido = LojaPedido::where("id", $req["id"])->update($req);
            echo json_encode($pedido);            
        } catch (\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
    
    public function pagarPorTransferencia(Request $request){
        $req = $request->all();
        try{
            $pedido = LojaPedido::where("id", $req["id"])->update($req);
            PagamentoService::gerarVenda($req["id"]);
            echo json_encode($pedido);            
        } catch (\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
    public function addItem(Request $request){
        $req = $request->all();
        try{
            $produto = Produto::find($req["produto_id"]);
            $req["valor"] = $produto->valor_venda;
            $item = LojaItemPedido::create($req);
            echo json_encode($item);
            
        } catch (\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
    
    
    public function salvarCliente(Request $request){
        $req = (object) $request->all();        
        try{
            $cliente  = LojaClienteService::salvar($req);         
            echo json_encode($cliente);
            
        } catch (\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
    
    public function salvarEnderecoCliente(Request $request){
        $req = $request->all();
        try{
            if(!$req["id"]){
                $endereco = LojaEnderecoCliente::create($req);
            }else{
                $endereco = LojaEnderecoCliente::where("id", $req["id"])->update($req);
            }
            echo json_encode($endereco);
            
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
    
    
    public function atualizarDadosCliente(Request $request){
        $req = $request->all();
        try{
            $cliente = LojaCliente::where("id", $req["id"])->update($req);                       
            echo json_encode($cliente);
            
        } catch (\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
    
    public function setPedidoClienteEndereco($id_pedido,$id_cliente, $id_endereco){
        $pedido = LojaPedido::where("id", $id_pedido)->update(["cliente_id"=>$id_cliente,"endereco_id"=>$id_pedido]);
        echo json_encode($pedido);
    }
	
	public function verificaSePedidoTemVenda($id_pedido){
		$tem = LojaPedido::where("id", $id_pedido)->where("venda_id",">",0)->first();
		
		$retorno = ($tem) ? true : false;
		echo json_encode($retorno);
	}
}
