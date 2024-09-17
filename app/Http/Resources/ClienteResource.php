<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    
    public function toArray($request)
    {
        
        return [           
            "nome"          => $this->nome_razao_social,
            "nome_fantasia" => $this->nome_fantasia,
            "email"         => $this->email ,              
            "cpf_cnpj"      => $this->cpf_cnpj,
            "cep"           => $this->cep ,
            "logradouro"    => $this->logradouro ,
            "numero"        => $this->numero ,
            "complemento"   => $this->complemento ,
            "cidade"        => $this->cidade ,
            "uf"            => $this->uf ,
            "uuid"          => $this->uuid ,
            "token"         => $this->empresa->uuid 
        ];
    }
    
   
}
