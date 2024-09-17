<?php
namespace App\Services;

use App\Models\FormaPagto;
use App\Models\PdvCaixa;
use App\Models\PdvDuplicata;
use App\Models\PdvPagamento;
use App\Models\PdvSangria;
use App\Models\PdvSuplemento;
use App\Models\PdvVenda;
use App\Repositorios\Contratos\PdvCaixaRepositorioInterface;
use App\Repositorios\Contratos\UsuarioRepositorioInterface;

class PdvCaixaService
{    
    protected $usuarioRepositorio;
    protected $pdvCaixaRepositorio;
    
    
    public function __construct(PdvCaixaRepositorioInterface $pdvCaixaRepositorio,
                                UsuarioRepositorioInterface $usuarioRepositorio) {
        $this->usuarioRepositorio = $usuarioRepositorio; 
        $this->pdvCaixaRepositorio = $pdvCaixaRepositorio;      
    }       
    
    public function listaCaixaAbertoPorUsuario(string $uuid){
        $usuario = $this->usuarioRepositorio->getUsuarioPorUuid($uuid);
        return $this->pdvCaixaRepositorio->listaCaixaAbertoPorUsuario($usuario->id);
    }
    
    public function getCaixa($caixa_id){
        return $this->pdvCaixaRepositorio->getCaixa($caixa_id);
    }
    
    public function podeFechar($caixa_id){
        $venda = PdvVenda::where("caixa_id" , $caixa_id)->where("status_id", "!=", config("constantes.status.CONCRETIZADO"))->first();
        return $venda ? "N" : "S";
    }
    
    public function listaSomaPorFormaPagto($id_caixa){
        //Pegando as formas de pagamento distintas
        $formas =  FormaPagto::whereIn('id', function($query) {
            $query->distinct();
            $query->select('pdv_duplicatas.tPag');
            $query->from('pdv_duplicatas');
        })->get();    
    
        foreach($formas as $f){            
            $soma = PdvDuplicata::where("tPag", $f->id)->where("caixa_id", $id_caixa)->sum("vDup");
            $f->total = $soma;
        }        
        return $formas;
    }
    
    public static function valores($caixa_id){
        $retorno = new \stdClass();
        $caixa                = PdvCaixa::find($caixa_id);
        $retorno->faturamento = PdvPagamento::where("caixa_id", $caixa_id)->sum("valor");
        $retorno->sangria     = PdvSangria::where("caixa_id", $caixa_id)->sum("valor");
        $retorno->suplemento  = PdvSuplemento::where("caixa_id", $caixa_id)->sum("valor");
        $retorno->troco       = $caixa->valor_abertura;
        $retorno->total       = $retorno->faturamento - $retorno->sangria + $retorno->suplemento + $retorno->troco;
        return $retorno;
    }
    
    public function verificaSeTemCaixaAbertoPorUsuario(string $uuid){
        $usuario = $this->usuarioRepositorio->getUsuarioPorUuid($uuid);
        return $this->pdvCaixaRepositorio->verificaSeTemCaixaAbertoPorUsuario($usuario->id);
    }
    
    public function abrir(array $dados){       
        $usuario = $this->usuarioRepositorio->getUsuarioPorUuid($dados["usuario_abriu_id"]);
        $dados["data_abertura"] 	   = hoje();
        $dados["hora_abertura"] 	   = agora();
        $dados["empresa_id"]           = $usuario->empresa_id;
        $dados["usuario_abriu_id"]     = $usuario->id;
        $dados["valor_fechamento"]     = $dados["valor_fechamento"] ?? 0 ;
        $dados["valor_vendido"]        = $dados["valor_vendido"] ?? 0 ;
        $dados["valor_quebra"]         = $dados["valor_quebra"] ??  0 ;
        $dados["valor_sangria"]        = $dados["valor_sangria"] ?? 0 ;
        $dados["valor_suplemento"]     = $dados["valor_suplemento"] ??  0 ;
        $dados["total_em_caixa"]       = $dados["total_em_caixa"] ?? 0 ;
        
        $dados["status_id"]            = config("constantes.status.ABERTO")  ;     
        
        $caixa = $this->pdvCaixaRepositorio->abrir($dados);
        
        return $caixa;
    }
    
    public function fechar($id_caixa, $uuid){
        $usuario = $this->usuarioRepositorio->getUsuarioPorUuid($uuid);
        $caixa = PdvCaixa::find($id_caixa);
        $caixa->data_fechamento     = hoje();
        $caixa->hora_fechamento     = agora();
        $caixa->valor_fechamento    = $caixa->total_em_caixa;
        $caixa->usuario_fechou_id   = $usuario->id;
        $caixa->status_id           = config("constantes.status.FECHADO")  ;
        $caixa->save();
        return $id_caixa;
    }
}

