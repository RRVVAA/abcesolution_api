<?php

namespace App\Http\Controllers\Acl;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoginResource;
use App\Http\Resources\VendedorResource;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use App\Models\Log; //adicionar recurso de log de sistema no futuro


class LoginController extends Controller{ 
    
    public function logarPdv(Request $req ){
        $data = $req->all();

        if ( Auth::attempt( [ 'email' => $data['email'] , 'password' => $data['password' ]] ) ) {
            $usuario =  Auth::user();
            return new LoginResource($usuario);
        }else{
            return response()->json(["data" =>null ], 404);
        }        
    }
    
    
    public function logarLoja(Request $req ){        
        $data = $req->all();      
        
        if ( Auth::attempt( [ 'email' => $data['email'] , 'password' => $data['password' ]] ) ) {
            $usuario =  Auth::user();
            return new LoginResource($usuario);
        }else{
            $retorno = new \stdClass();
            $retorno->tem_erro = true;
            $retorno->erro = "Usuário/Senha Não Encontrado";
            return response()->json(["data" =>$retorno ], 404);
        }
        
    }
    
    public function logarBalcao(Request $req ){
        $data = $req->all();
        $senha = md5($data['password' ]);
        
        $vendedor = Vendedor::where(["email"=>$data['email'],'password' => $senha] )->first();        
        if ( $vendedor ) {            
            return new VendedorResource($vendedor);
        }else{
            $retorno = new \stdClass();
            $retorno->tem_erro = true;
            $retorno->erro = "Usuário/Senha Não Encontrado";
            return response()->json(["data" =>$retorno ], 404);
        }
    }
    
    
    public function login(Request $req ){       
        $this->validate($req, ['email'=>'required', 'password'=>'required']);        
        $data = $req->all();   
       
    if ( Auth::attempt( [ 'email' => $data['email'] , 'password' => $data['password' ]] ) ) {
            $usuario_logado = Auth::user(); 
            i($usuario_logado);         
            
        }
    }
    public function logout () {
       // Auth::logout();
        return redirect()->route('login');
    }

}
