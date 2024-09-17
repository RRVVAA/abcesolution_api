<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\VendaRecorrente;

class CobrancaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $venda_recorrente = VendaRecorrente::find($this->venda_recorrente_id);
        return [
            "id"                    => $this->id,
            "empresa_id"            => $venda_recorrente->empresa_id,
            "mercadopago_access_token" => $venda_recorrente->empresa->parametro->mercadopago_access_token ?? null,
            "mercadopago_public_key"   => $venda_recorrente->empresa->parametro->mercadopago_public_key ?? null,
            "uuid"                  => $this->uuid,
            "descricao"             => $this->descricao,
            "venda_recorrente_id"   => $this->venda_recorrente_id,
            "cliente_id"            => $this->cliente_id,
            "status"                => $this->status,
            "valor"                 => $this->valor, 
            "data_cadastro"         => $this->data_cadastro,
            "data_vencimento"       => $this->data_vencimento,
            "data_pagamento"        => $this->data_pagamento,
            "status_financeiro"     => $this->status_financeiro,   
            "cliente"               => new ClienteResource($this->cliente),
        ];
    }
    
   
}
