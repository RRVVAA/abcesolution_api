<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProdutoResource extends JsonResource
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
            "uuid"          => $this->uuid,
            "nome"          => $this->nome,
            "imagem"        => $this->imagem,
            "preco"         => $this->valor_venda,
            "unidade"       => $this->unidade,
            "cod_barra"     => $this->codigo_barra,
            "unidade"       => $this->unidade,
            "referencia"    => $this->referencia,
            "ncm"           => $this->ncm,
            "largura"       => $this->largura,
            "altura"        => $this->altura,
            "comprimento"   => $this->comprimento,
            "peso_liquido"  => $this->peso_liquido,
            "peso_bruto"    => $this->peso_bruto,    
            "descricao"     => $this->descricao,
            "categoria"     => new CategoriaResource($this->categoria),
            
            
        ];
    }
    
   
}
