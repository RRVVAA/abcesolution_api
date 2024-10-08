<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
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
            "uuid"      => $this->uuid,
            "categoria" => $this->categoria,                       
            "created_at"=> Carbon::parse($this->created_at)->format("d/m/y")
        ];
    }
    
   
}
