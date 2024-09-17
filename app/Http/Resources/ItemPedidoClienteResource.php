<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemPedidoClienteResource extends JsonResource
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
            "id"            => $this->id,
            "produto"       => new ProdutoResource($this->produto),
            "qtde"          => $this->qtde ,  
            "valor"         => $this->valor,
            "subtotal"      => $this->subtotal
        ];
    }
    
   
}
