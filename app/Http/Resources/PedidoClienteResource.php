<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PedidoClienteResource extends JsonResource
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
            "identificador"     => $this->identificador,
            "total"             => $this->total,
            "origem"            => $this->origem,
            "observacao"        => $this->observacao,
            "cliente"           => new ClienteResource($this->cliente) ,
            "data_atendimento"  => $this->data_atendimento,
            "data_pedido"       => $this->data_pedido,
            "hora_pedido"       => $this->hora_pedido,
            "status"            => $this->status,
            "itens"             => ItemPedidoClienteResource::collection($this->itens),
        ];
    }
    
   
}
