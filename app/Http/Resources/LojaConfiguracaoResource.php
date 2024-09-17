<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LojaConfiguracaoResource extends JsonResource
{
    
    public function toArray($request)
    {
        return [   
            "nome"          => $this->nome,
            "link"          => $this->link,
            "logo"          => $this->logo,
            "rua"           => $this->rua,
            "numero"        => $this->numero,
            "bairro"        => $this->bairro,
            "cidade"        => $this->cidade,
            "uf"            => $this->uf,
            "cep"           => $this->cep,
            "telefone"      => $this->telefone,
            "email"         => $this->email,
            "link_facebook" => $this->link_facebook,
            "link_twiter"   => $this->link_twiter,
            "link_instagram"=> $this->link_instagram,              
            
        ];
    }
    
   
}
