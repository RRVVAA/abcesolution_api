<?php
namespace App\Services;


use App\Http\Resources\PdvVendaResource;
use App\Models\PdvCaixa;
use App\Models\PdvVenda;
use App\Models\User;
use App\Models\Produto;
use App\Models\Categoria;

class FrenteService
{
    public static function mostrarNaFrente($dados){
        $acao           = $dados->acao;        
        $usuario        = User::where("uuid",$dados->usuario_uuid)->first();        
        $venda          = null;
        $caixa          = null;
        $produtos       = Produto::where("empresa_id", $usuario->empresa_id)->get();
        $categorias     = Categoria::where("empresa_id", $usuario->empresa_id)->get();
       
        if($acao=="verificar_venda_aberta" ){
            $venda          = PdvVenda::where("usuario_id", $usuario->id)->where("caixa_id", $dados->caixa_id)->where("status_id", config("constantes.status.DIGITACAO"))->first();
            if($venda){
                $retorno                = new \stdClass();
                $retorno->venda         = new PdvVendaResource($venda);
                $retorno->produtos      = $produtos;
                $retorno->categorias    = $categorias;                
                return $retorno;
            }
                        
        }
        
        if($acao=="ver_caixa" ){
            
            $retorno                = new \stdClass();
            $retorno->venda         = $venda;
            $retorno->caixa         = PdvCaixa::where("id",$dados->caixa_id)->with("status")->first();  
            return  $retorno;
        }
        
        $retorno                = new \stdClass();
        $retorno->venda         = $venda;
        $retorno->caixa         = $caixa;
        $retorno->produtos      = $produtos;
        $retorno->categorias    = $categorias; 
        return  $retorno;
    }
    
}

