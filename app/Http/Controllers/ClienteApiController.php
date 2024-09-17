<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Http\Resources\ClienteResource;
use App\Services\ClienteService;

class ClienteApiController extends Controller
{
    protected $clienteService;
    public function __construct(ClienteService $clienteService){
        $this->clienteService = $clienteService;
    } 
    
    public function logar(ClienteRequest $request){ 
        $cliente = $this->clienteService->logar($request->email, $request->password);
        if(!$cliente){
            return response()->json(["data" =>"", "erro" => "Nenhum Caixa Encontrada"], 404);
        }        
        return new ClienteResource($cliente);        
    }
    
}
