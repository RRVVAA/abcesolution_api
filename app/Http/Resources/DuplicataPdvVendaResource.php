<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DuplicataPdvVendaResource extends JsonResource
{
    
    public function toArray($request)
    {
        return [           
            "id"                => $this->id,
            "pagto"             => new FormaPagamentoResource($this->forma_pagto),
            "nDup"              => $this->nDup ,  
            "dVenc"             => $this->dVenc,
            "vDup"              => $this->vDup,
        ];
    }
    
   
}
