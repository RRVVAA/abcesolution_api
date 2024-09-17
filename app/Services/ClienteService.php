<?php
namespace App\Services;

use App\Repositorios\Contratos\ClienteRepositorioInterface;
use App\Repositorios\Contratos\EmpresaRepositorioInterface;
use App\Models\Cliente;

class ClienteService
{
    protected $empresaRepositorio;
    protected $clienteRepositorio;
    
    public function __construct(ClienteRepositorioInterface $clienteRepositorio, 
                                EmpresaRepositorioInterface $empresaRepositorio) {
        $this->clienteRepositorio = $clienteRepositorio;
        $this->empresaRepositorio = $empresaRepositorio;
        
    }        
    
    public function logar($email, $senha, $uuid=null){
        $cliente = Cliente::where(['email' => $email ] )->first();
        return $cliente;
    }
    
    public function getClientePorUuid(string $uuid){
        return $this->clienteRepositorio->getClientePorUuid($uuid);
    }
    
    public static function buscaCliente($dados){
        $campo = $dados->campo;
        $valor = $dados->valor;
        
        $lista = Cliente::where($campo, 'like', '%' . $valor .'%')->get();
        $retorno = new \stdClass();
        $retorno->lista = $lista;
        
        return $retorno;
        
    }
}

