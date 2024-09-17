<?php

namespace App\Http\Controllers;

use App\Http\Resources\LojaConfiguracaoResource;
use App\Services\LojaConfiguracaoService;

class LojaConfiguracaoApiController extends Controller
{    
    
    public function show($token){       
        $lojaConfiguracao = LojaConfiguracaoService::getLojaConfiguracaoPorEmpresaUuid($token);        
        if(!$lojaConfiguracao){
            return response()->json(["message" => "LojaConfiguracao Não Encontrada"], 404);
        }
        return new LojaConfiguracaoResource($lojaConfiguracao);
    }
    
}
