<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendedorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "tem_erro"      => false,
            "id"            => $this->id ,
            "nome"          => $this->nome,
            "cpf"           => $this->cpf,
            "email"         => $this->email ,  
            "celular"       => $this->celular ,
            "uuid"          => $this->uuid ,
            "token"         => $this->empresa->uuid ,
        ];
    }
    
   
}
