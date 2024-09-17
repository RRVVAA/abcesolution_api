<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use App\Models\Log; //adicionar recurso de log de sistema no futuro


class LoginController extends Controller{
    public function index(){
        return view('Login');
    }   
    
    
    public function loginExterno(Request $request){
       
        $email          = $data['email'];
        $senha          = $rq['password'];
        
        $senha_criptografada = crypt($senha, $usuario_logado->getAuthPassword());        
        return $senha_criptografada == $usuario_logado->getAuthPassword();      
    }
    
    public function login(Request $req ){       
        $this->validate($req, ['email'=>'required', 'password'=>'required']);        
        $data = $req->all();
      
        if ( Auth::attempt( [ 'email' => $data['email'] , 'password' => $data['password' ]] ) ) {
            $usuario_logado = Auth::user();       
         
            //if ( $usuario_logado->isAtivo() ) {                
                return redirect()->route('home');
            //} else {
            //    Auth::logout();              
           //     return redirect('login')->with('msg_erro', 'Usuário inativo.');
           // }
        }

         return redirect()->back()->with('msg_erro', 'Usuário ou senha inválidos.');
        //return redirect()->route('home');
    }
    public function logout () {
        Auth::logout();
        return redirect()->route('login');
    }

}
