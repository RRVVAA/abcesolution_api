<?php

namespace App\Http\Controllers;

use App\Services\EmpresaService;
use App\Http\Resources\EmpresaResource;

class EmpresaApiController extends Controller
{
    protected $empresaService;
    public function __construct(EmpresaService $empresaService){
        $this->empresaService = $empresaService;
    }
    
    public function index(){
        return EmpresaResource::collection($this->empresaService->getAllEmpresas());
    }
    
    public function show($uuid){
        $empresa = $this->empresaService->getEmpresaPorUuid($uuid);
        if(!$empresa){
            return response()->json(["message" => "Não Encontrado"], 404);
        }
        return new EmpresaResource($empresa);
    }
    
}
