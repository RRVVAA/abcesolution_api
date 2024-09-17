<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PdvCaixaResource extends JsonResource
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
            "id"                => $this->id,
            "data_abertura"     => $this->data_abertura,
            "hora_abertura"     => $this->hora_abertura,
            "valor_abertura"    => $this->valor_abertura ,  
            "data_fechamento"   => $this->data_fechamento ,
            "valor_vendido"     => $this->valor_vendido ,
            "dinheiro_gaveta"   => $this->dinheiro_gaveta ,
            "hora_fechamento"   => $this->hora_fechamento,
            "num_pdv"           => $this->num_pdv,
            "status"            => $this->status,
            
        ];
    }
    
   
}
