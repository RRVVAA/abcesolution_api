<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendaBalcaoResource extends JsonResource
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
            "id"                    => $this->id,
            "cliente_id"            => $this->cliente_id,
            "usuario_id"            => $this->usuario_id,
            "data_venda"            => $this->data_venda,
            "valor_liquido"         => $this->valor_liquido,
            "valor_frete"           => $this->valor_frete,
            "desconto_valor"        => $this->desconto_valor,
            "desconto_per"          => $this->desconto_per,
            "valor_desconto"        => $this->valor_desconto,
            "valor_total"           => $this->valor_total,
            "status"                => new StatusResource($this->status),            
            "vendedor"              => new VendedorResource($this->vendedor),
            "itens"                 => ItemVendaBalcaoResource::collection($this->itens)        
         ];
    }
    
   
}
