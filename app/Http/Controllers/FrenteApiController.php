<?php

namespace App\Http\Controllers;

use App\Services\FrenteService;
use Illuminate\Http\Request;

class FrenteApiController extends Controller
{      
    
    public function home(Request $request){
        $dados = (object) $request->all();
        $resultado = FrenteService::mostrarNaFrente($dados);
        return response()->json(["data" =>$resultado], 404);
    }
    
   
}
