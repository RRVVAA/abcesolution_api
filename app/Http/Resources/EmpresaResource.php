<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class EmpresaResource extends JsonResource
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
            
            "uuid"          => $this->uuid,
            "razao_social"  => $this->razao_social,
            "cpf_cnpj"      => $this->cpf_cnpj,
            "email"         => $this->email,
            "logo"          => $this->logo ? url("storage/$this->logo"): "",
            "pasta"         => $this->pasta,
            "configurado"   => $this->configurado,
            "mostrar_pendencia"=> $this->mostrar_pendencia,
            "cep"           => $this->cep,
            "logradouro"    => $this->logradouro,
            "numero"        => $this->numero,
            "bairro"        => $this->bairro,
            "complemento"   => $this->complemento,
            "uf"            => $this->uf,
            "cidade"        => $this->cidade,
            "fone"          => $this->fone,
            "celular"       => $this->celular,
            "status_id"     => $this->status_id,
            "status_plano_id"=> $this->status_plano_id,
            "forma_pagto_id"=> $this->forma_pagto_id,
            "data_aquisicao"=> $this->data_aquisicao,
            "valor_contrato"=> $this->valor_contrato,
            "data_vencimento"=>$this->data_vencimento,
            "data_inicial_vencimento"=> $this->data_inicial_vencimento,
            "valor_recorrente"=> $this->valor_recorrente,
            "dias_bloqueia"=> $this->dias_bloqueia,
            "plano_preco_id"=> $this->plano_preco_id,
            "created_at"=> Carbon::parse($this->created_at)->format("d/m/y")
        ];
    }
    
   
}
