<?php
namespace App\Services;

use App\Models\Empresa;
use App\Models\LojaConfiguracao;

class LojaConfiguracaoService
{          
    
    public static function getLojaConfiguracaoPorEmpresaUuid(string $uuid){
        $empresa = Empresa::where("uuid",$uuid)->first();     
        return  LojaConfiguracao::where("empresa_id",$empresa->id)->first();
    }    
    
}

