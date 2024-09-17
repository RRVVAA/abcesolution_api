<?php

namespace App\Http\Controllers;

use App\Http\Resources\PdvNumeroResource;
use App\Services\PdvNumeroService;

class PdvNumeroApiController extends Controller
{
    protected $pdvNumeroService;
    public function __construct(PdvNumeroService $pdvNumeroService){
        $this->pdvNumeroService = $pdvNumeroService;
    }    
    
    
    public function lista($token){
        $retorno = $this->pdvNumeroService->listaPdvNumeroPorEmpresa($token);        
        return PdvNumeroResource::collection($retorno);
    }
    
    
    
    
}
