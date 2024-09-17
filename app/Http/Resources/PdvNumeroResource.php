<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PdvNumeroResource extends JsonResource
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
            "id"  => $this->id,
            "num_caixa"         => $this->num_caixa,
            "descricao"         => $this->descricao,
            "padrao_busca"      => $this->padrao_busca,
            "transmitir_nfce"   => $this->transmitir_nfce,
            "gerar_financeiro"  => $this->gerar_financeiro,
            "gerar_estoque"     => $this->gerar_estoque,
            "apos_a_venda"      => $this->apos_a_venda,
            
        ];
    }
    
   
}
