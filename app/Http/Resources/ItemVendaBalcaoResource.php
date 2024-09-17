<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemVendaBalcaoResource extends JsonResource
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
            "produto"           => new ProdutoResource($this->produto),
            "venda_id"          => $this->venda_balcao_id ,  
            "qtde"              => $this->quantidade ,  
            "valor"             => $this->valor,
            "subtotal"          => $this->subtotal,
            "subtotal_liquido"     => $this->subtotal_liquido,
            "desconto_percentual"=> $this->desconto_percentual,
            "desconto_por_valor"  => $this->desconto_por_valor,
            "desconto_por_unidade"  => $this->desconto_por_unidade,
            "unidade"           => $this->unidade
        ];
    }
    
   
}
