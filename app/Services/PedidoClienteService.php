<?php
namespace App\Services;

use App\Http\Resources\PedidoClienteResource;
use App\Models\VendaRecorrente;
use App\Repositorios\Contratos\ClienteRepositorioInterface;
use App\Repositorios\Contratos\EmpresaRepositorioInterface;
use App\Repositorios\Contratos\PedidoClienteRepositorioInterface;
use App\Repositorios\Contratos\ProdutoRepositorioInterface;

class PedidoClienteService
{
    protected $pedidoClienteRepositorio , $empresaRepositorio, $produtoRepositorio, $clienteRepositorio ;
    
    public function __construct(
            PedidoClienteRepositorioInterface $pedidoClienteRepositorio,
            EmpresaRepositorioInterface $empresaRepositorio,
            ProdutoRepositorioInterface $produtoRepositorio,
            ClienteRepositorioInterface $clienteRepositorio
        ) {
        $this->pedidoClienteRepositorio = $pedidoClienteRepositorio; 
        $this->empresaRepositorio       = $empresaRepositorio; 
        $this->produtoRepositorio       = $produtoRepositorio;
        $this->clienteRepositorio       = $clienteRepositorio;
    }        
    
    public function filtro(array $dados){
        $cliente        = $this->clienteRepositorio->getClientePorUuid($dados["uuid"]);
        $retorno                = new \stdClass();
        $retorno->assinaturas   = array();
        $retorno->pedidos       = array();
        if($cliente){
            $assinaturas            = VendaRecorrente::where("cliente_id", $cliente->id)->get();
            $pedidos                = $this->pedidoClienteRepositorio->filtro($dados["data1"], $dados["data2"], $cliente->id);
            $retorno->assinaturas   = $assinaturas;
            $retorno->pedidos       = PedidoClienteResource::collection($pedidos);
        }        
        
        return      $retorno;
             
    }
    
    public function criarNovoPedido(array $pedido){
        $itens          = $this->getProdutosPorPedido($pedido["itens"]) ?? [];
        
        $identificador  = $this->getIdentificadorPedido(8);
        $total          = $this->getTotalPedido($itens);
       
        $status_id      = config("constantes.status.ABERTO");
        $empresa_id     = $this->getEmpresaPedido($pedido["token"])->id;
        $cliente_id     = $this->clienteRepositorio->getClientePorUuid($pedido["cliente_uuid"])->id  ;
        $origem         = $pedido["origem"];
        $observacao     = $pedido["observacao"] ?? null;
        $pedido = $this->pedidoClienteRepositorio->criarNovoPedido(
            $identificador, $total, $status_id, $empresa_id, $cliente_id, $origem, $observacao);
        
        $this->pedidoClienteRepositorio->inserirItensPedido($pedido->id, $itens);
        return $pedido;
    }
    
    public function getPedidoPorIdentificador(string $identificador){
        return $this->pedidoClienteRepositorio->getPedidoPorIdentificador($identificador);
    }
    
    private function getIdentificadorPedido(int $qtyCaraceters = 8){
        $smallLetters = str_shuffle('abcdefghijklmnopqrstuvwxyz');
        
        $numbers = (((date('Ymd') / 12) * 24) + mt_rand(800, 9999));
        $numbers .= 1234567890;
        
        $characters = $smallLetters.$numbers;
        
        $identificador = substr(str_shuffle($characters), 0, $qtyCaraceters);
        
        if ($this->pedidoClienteRepositorio->getPedidoPorIdentificador($identificador)) {
            $this->getIdentificadorPedido($qtyCaraceters + 1);
        }
        
        return $identificador;
    }
   
    private function getProdutosPorPedido(array $produtosPedido): array
    {
        $produtos = [];
        foreach ($produtosPedido as $produtoPedido) {
            $produto = $this->produtoRepositorio->getProdutoPorUuid($produtoPedido['produto_uuid']);
            
            array_push($produtos, [
                'produto_id'=> $produto->id,
                'qtde'      => $produtoPedido['qtde'],
                'valor'     => $produto->valor_venda,
            ]);
        }
       
        return $produtos;
    }
    
    private function getTotalPedido(array $produtos):float{
        $total = 0;        
        foreach ($produtos as $produto) {
            $total += ($produto['valor'] * $produto['qtde']);
        }        
        return (float) $total;
    }
    
    private function getEmpresaPedido(string $uuid){
        $empresa = $this->empresaRepositorio->getEmpresaPorUuid($uuid);
        return $empresa;
    }    
   
}

