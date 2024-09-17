<?php

namespace App\Http\Resources;

use App\Models\Emitente;
use App\Models\PdvCaixa;
use App\Services\NfceRequisitoService;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {        
        $emitente       = Emitente::where("empresa_id", $this->empresa->id)->first(); 
        $caixa          = $caixa = PdvCaixa::where("usuario_abriu_id", $this->id)->where("status_id", config("constantes.status.ABERTO"))->first();
        $caixa_id       = $caixa->id ?? null;
        $caixanumero_id = $caixa->caixanumero_id ?? null;
        $num_caixa      = $caixa->num_pdv->num_caixa ?? null;
        $descricao_caixa= $caixa->num_pdv->descricao ?? null;
        $padrao_busca   = $caixa->num_pdv->padrao_busca ?? null;
        $transmitir_nfce= $caixa->num_pdv->transmitir_nfce ?? "N"; 
        $pendencias     = NfceRequisitoService::pendencia($this->empresa_id);
        return [ 
            "uuid"          => $this->uuid,
            "nome"          => $this->name,
            "email"         => $this->email ,  
            "admin"         => $this->eh_admin ,
            "foto"          => $this->foto,
            "empresa_uuid"  => $this->empresa->uuid,
            "empresa_pasta" => $this->empresa->pasta,
            "ambiente"      => $emitente->ambiente_nfe,
            "ambiente"      => $emitente->ambiente_nfce,
            "empresa_id"    => $this->empresa_id,
            "cliente_consumidor" => $emitente->cliente_consumidor,
            "conta_corrente_id" => $emitente->pdv_conta_corrente_id ,
            "classificacao_financeira_id" => $emitente->pdv_classificacao_financeira_id,
            "permissoes"    =>[],
            "caixa_id"      =>$caixa_id,
            "imprim"        =>$caixa_id,
            "caixanumero_id"=>$caixanumero_id,
            "descricao_caixa"=>$descricao_caixa,
            "transmite_nfce"=>$transmitir_nfce,
            "padrao_busca"  =>$padrao_busca,
            "num_caixa"     =>$num_caixa,
            "pendencias"    =>$pendencias
        ];
    }
    
   
}
