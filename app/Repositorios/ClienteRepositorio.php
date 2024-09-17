<?php
namespace App\Repositorios;

use App\Models\Cliente;
use App\Repositorios\Contratos\ClienteRepositorioInterface;

class ClienteRepositorio implements ClienteRepositorioInterface{
    protected $entidade;
    
    
    public function __construct(Cliente $cliente){
          $this->entidade = $cliente;  
    }
    public function clientesPorEmpresaUuid($uuid_empresa)
    {
        return $this->entidade->join("empresas","empresas.id", "*", "clientes.empresa_id")
        ->where("empresas.uuid", $uuid_empresa)
                ->select("clientes.*")
                ->get();
    }    
    
    public function getClientesPorEmpresaId($id_empresa)
    {
        return $this->entidade->where("empresa_id", $id_empresa)->get();
    }
    
    public function getClientePorUuid($uuid){
        return $this->entidade->where("uuid", $uuid)->first();
    }
    
    public function logar($email, $senha){  
        //$cliente = $this->entidade->where(['email' => $email , 'password' => $senha, "empresa_id" => $empresa_id ] )->first();               
        $cliente = $this->entidade->where(['email' => $email ] )->first();
        return $cliente;
    }
    
}

