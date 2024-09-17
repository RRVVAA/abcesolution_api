<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PedidoClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "cliente_uuid" =>"required",
            "origem"   =>"required",
            "token" => [
                "required",
                "exists:empresas,uuid"
                ],
            
            "observacao" => [
                "nullable",
                "max:1000"
            ],
            'itens' =>["required"],
            "itens.*.produto_uuid" =>["required","exists:produtos,uuid" ],
            "itens.*.qtde" =>["required" ]
        ];
    }
}
