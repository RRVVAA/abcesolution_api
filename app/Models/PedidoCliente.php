<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PedidoCliente extends Model
{
    use HasFactory;
    protected $fillable =
    [
        "id",
        "empresa_id",
        "identificador",
        "observacao",
        "status_id",
        "cliente_id",
        'data_pedido',
        "hora_pedido",
        "baixado",
        "finalizado",
        "total","origem"        
    ];
    
    public function empresa(){
        return $this->belongsTo(Empresa::class,"empresa_id","id");
    }
    
    public function cliente(){
        return $this->belongsTo(Cliente::class,"cliente_id","id");
    }    
    
    
    
    public function status(){
        return $this->belongsTo(Status::class,"status_id","id");
    }
    public function produtos(){
        return $this->hasMany(Produto::class,"produto_id", "id");
    }
    
    public function itens(){
        return $this->hasMany(ItemPedidoCliente::class,"pedido_id", "id");
    }
    
    public static function filtro($filtro){
        
        $retorno = PedidoCliente::orderby('pedido_clientes.data_pedido',"asc");
        
        if($filtro->cliente_id){
            $retorno->where("cliente_id", $filtro->cliente_id);
        }
        
        if($filtro->status_id){
            $retorno->where("status_id", $filtro->status_id);
        }
        if($filtro->data2){
            if($filtro->data2){
                $retorno->where("data_pedido",">=", $filtro->data1)->where("data_pedido","<=", $filtro->data2);
            }else{
                $retorno->where("data_pedido", $filtro->data1);
            }
        }
        return $retorno->get();
    }
}
