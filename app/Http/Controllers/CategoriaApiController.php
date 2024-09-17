<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriaRequest;
use App\Http\Resources\CategoriaResource;
use App\Services\CategoriaService;

class CategoriaApiController extends Controller
{
    protected $categoriaService;
    public function __construct(CategoriaService $categoriaService){
        $this->categoriaService = $categoriaService;
    }    
    
    
    public function categoriasPorEmpresa($token){
        return CategoriaResource::collection($this->categoriaService->getCategoriaPorEmpresaUuid($token));
    }
    
    public function show(CategoriaRequest $request, $uuid){
        $categoria= $this->categoriaService->getCategoriaPorUuid($uuid);
        if(!$categoria){
            return response()->json(["data" =>"","message" => "Categoria Não Encontrada"], 404);
        }
        return new CategoriaResource($categoria);
    }
    
}
