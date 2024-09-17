<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\PdvItemVenda;
use App\Models\PdvDuplicata;

class PdvVendaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        
        $valor_pago     = PdvDuplicata::where("venda_id", $this->id)->sum("vDup");
        $valor_restante = $this->valor_liquido - $valor_pago;
        $limite         = $this->cliente->credito_disponivel ?? null;
        $nome           = $this->cliente->nome_razao_social ?? null;
        $cpf            = $this->cliente->cpf_cnpj ?? null;
        $consumidor     = ($this->cliente->eh_consumidor ?? null) == "S" ? "S" : "N";
        $situacao       = $this->cliente->credito_liberado ?? null;
        $cupom          = $this->cupom->codigo ?? null;
        
        return [ 
            "id"                => $this->id,
            "vendedor_id"       => $this->vendedor_id,
            "cupom_desconto_id" => $this->cupom_desconto_id,
            "codigo_cupom"      => $cupom,
            "valor_venda"       => $this->valor_venda,  
            "data_venda"        => $this->data_venda ,
            "cliente_nome"      => $this->cliente_nome,
            "cliente_cpf"       => $this->cliente_cpf,
            "cliente_cnpj"      => $this->cliente_cnpj,
            "valor_liquido"     => $this->valor_liquido,
            "valor_desconto"    => $this->valor_desconto,
            "desconto_valor"    => $this->desconto_valor,
            "desconto_per"      => $this->desconto_per,            
            
            "valor_acrescimo"   => $this->valor_acrescimo,
            "acrescimo_valor"   => $this->acrescimo_valor,
            "acrescimo_per"     => $this->acrescimo_per,
            
            "valor_pago"        => $valor_pago,
            "valor_restante"    => formataNumero($valor_restante),
            "qtde_volume"       => PdvItemVenda::where("venda_id", $this->id)->sum("qtde"),
            "itens"             => ItemPdvVendaResource::collection($this->itens),
            "duplicatas"        => DuplicataPdvVendaResource::collection($this->duplicatas),
            "caixa"             => new PdvCaixaResource($this->caixa),
            "cliente_id"        => $this->cliente_id,
            "nome_cliente"      => $nome,
            "limite"            => $limite,
            "cpf"               => $cpf,
            "cliente_consumidor"=> $consumidor,
            "situacao"          => $situacao,
         ];
    }
    
   
}
