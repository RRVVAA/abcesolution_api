<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LojaPedidoResource extends JsonResource
{
    
    public function toArray($request)
    {
        return [   
            "path"          => $this->path,
            "titulo"        => $this->titulo,
            "descricao"     => $this->descricao,
            "status_id"     => $this->status_id ,            
            
        ];
    }
    
   
}
