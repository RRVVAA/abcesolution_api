<?php
namespace App\Services;


use App\Models\Empresa;
use App\Models\ItemVendaBalcao;
use App\Models\VendaBalcao;

class VendaBalcaoService{
    
    public static function gerarNovoPedido($dados){
        $empresa = Empresa::where("uuid", $dados["token"])->first();
        if($empresa){
            $venda = VendaBalcao::where(["vendedor_id" =>$dados["id"],"empresa_id" => $empresa->id, "status_id"=> config('constantes.status.DIGITACAO')])->first();     
            if(!$venda){
                $venda = VendaBalcao::Create([
                                "empresa_id"    =>$empresa->id, 
                                "vendedor_id"   =>$dados["id"],
                                "data_venda"    => hoje(),
                                "valor_liquido"   => 0,
                                "desconto_valor"=> 0,
                                "desconto_per"  => 0,
                                "valor_desconto"=> 0,
                                "valor_liquido" => 0,
                                "status_id"=> config('constantes.status.DIGITACAO'),
                ]);
            }
        }
        
        return $venda;
    }
    
    public static function buscarPedido($id){
        return  VendaBalcao::find($id);
    }
    
    public static function inserirItem($dados){
        $item                    = new \stdClass();
        $item->venda_balcao_id   = $dados->venda_balcao_id;
        $item->produto_id        = $dados->produto_id;
        $item->quantidade        = $dados->quantidade;
        $item->unidade           = $dados->unidade ?? null;
        $item->valor             = $dados->valor;
        $item->subtotal          =  $dados->valor * $dados->quantidade;
        $item->valor_desconto    = 0;
        $item->desconto_por_unidade = 0;
        $item->desconto_por_valor = $dados->desconto_por_valor;
        $item->desconto_percentual = $dados->desconto_percentual;        
        
        if($item->desconto_por_valor > 0){
            $item->desconto_por_unidade = $dados->desconto_por_valor;
        }
        if($item->desconto_percentual > 0){
            $item->desconto_por_unidade = $dados->desconto_percentual * $item->valor * 0.01;
        }         
        
        $item->subtotal_liquido  =  ($item->valor - $item->desconto_por_unidade )  * $item->quantidade;
        $item->total_desconto_item  =  $item->desconto_por_unidade  * $item->quantidade; 
        return ItemVendaBalcao::Create(objToArray($item));
    }
    
    public static function excluirItem($dados){        
        //$item = ItemVendaBalcao::where(["id"=>$dados->id])->first();
        
        //if($item->venda->vendedor_id == $dados->vendedor_id){
            ItemVendaBalcao::where(["id"=>$dados->id])->delete();
        //}
       
    }
    
    public static function finalizarPedido($venda_balcao_id){
        return VendaBalcao::where(["id"=>$venda_balcao_id])->update(["status_id"=>config('constantes.status.FINALIZADO')]);
    }
    
    public static function cancelarPedido($venda_balcao_id){
        ItemVendaBalcao::where("venda_balcao_id",$venda_balcao_id)->delete();
        VendaBalcao::where(["id"=>$venda_balcao_id])->delete();
    }
    
    public static function excluirBalcao($dados){
        $venda = VendaBalcao::where(["id"=>$dados->venda_id])->first();
        ItemVendaBalcao::where("venda_balcao_id",$dados->venda_id)->delete();
        $venda->delete();
    }
}

