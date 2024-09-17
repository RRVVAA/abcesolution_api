<?php
namespace App\Repositorios;

use App\Models\PedidoCliente;
use App\Repositorios\Contratos\PedidoClienteRepositorioInterface;
use Illuminate\Support\Facades\DB;

class PedidoClienteRepositorio implements PedidoClienteRepositorioInterface
{
    protected $entidade;    
    
    
    public function __construct(PedidoCliente $pedidoCliente){
          $this->entidade = $pedidoCliente;  
    }
    
    public function getPedidoPorIdentificador($identificador)
    {
        return $this->entidade->where("identificador", $identificador)->first();
    }

    public function filtro($data1, $data2, $cliente_id){
        $retorno = $this->entidade->where('pedido_clientes.cliente_id', $cliente_id);       
        if($data2){
            if($data2){
                $retorno->where("data_pedido",">=", $data1)->where("data_pedido","<=", $data2);
            }else{
                $retorno->where("data_pedido", $data1);
            }
        }
        return $retorno->get();
        
    }
    public function criarNovoPedido(
            string $identificador, 
            float $total, 
            string $status_id, 
            int $empresa_id, 
            $cliente_id, 
            string $origem,
            $observacao
     )
    {
       
        $pedido = $this->entidade->create([
            "identificador" =>$identificador,
            "total" =>$total,
            "status_id" =>$status_id,
            "empresa_id" =>$empresa_id,
            "cliente_id" =>$cliente_id,
            "origem" =>$origem,
            "observacao" => $observacao,
            "data_pedido" => date("Y-m-d"),
            "hora_pedido" => date("H:i:s")
        ]);
        
     
        return $pedido;
    }
    public function inserirItensPedido(int $id_pedido, array $itens)
    {      
        
        $itensPedido = [];        
        foreach ($itens as $item) {
            array_push($itensPedido, [
                'pedido_id' => $id_pedido,
                "produto_id" =>$item["produto_id"],
                'qtde'  => $item['qtde'],
                'valor' => $item['valor'],
                'subtotal' => $item['valor'] * $item['qtde'],
                'valor' => $item['valor']]);           
        }
        DB::table('item_pedido_clientes')->insert($itensPedido);        
      
    }
    


    
}

