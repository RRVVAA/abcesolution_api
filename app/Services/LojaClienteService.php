<?php
namespace App\Services;

use App\Models\LojaCliente;
use App\Models\LojaEnderecoCliente;
use App\Models\LojaPedido;

class LojaClienteService{
    public static function salvar($dados){
        $cli = new \stdClass();
        $cli->nome       = $dados->nome;
        $cli->cpf        = $dados->cpf;
        $cli->email      = $dados->email;
        $cli->telefone   = $dados->telefone;
        $cli->senha      = $dados->senha;
        $cli->password   = bcrypt($dados->senha);
        $cli->empresa_id = $dados->empresa_id;
        $cli->status     = config("constantes.status.ATIVO");
        $cliente         = LojaCliente::Create(objToArray($cli));
        if($cliente){
            $end = new \stdClass();
            $end->rua        = $dados->rua;
            $end->numero     = $dados->numero;
            $end->bairro     = $dados->bairro;
            $end->cep        = $dados->cep;
            $end->cidade     = $dados->cidade;
            $end->uf         = $dados->uf;
            $end->complemento= $dados->complemento ?? '';
            $end->empresa_id = $dados->empresa_id;
            $end->cliente_id = $cliente->id;
            $end             = LojaEnderecoCliente::create(objToArray($end));            
            if($dados->pedido_id){
                LojaPedido::where("id", $dados->pedido_id)->update(["cliente_id"=>$cliente->id,"endereco_id"=>$end->id]);
            }
        }
        
        return $cliente;
        
        
    }
}

