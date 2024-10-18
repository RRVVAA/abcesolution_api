<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComandaPedido extends Model
{
    use HasFactory;
    protected $fillable =[
        'empresa_id',
        'pedido_id',
        'comanda_id',
        'cliente_id',
        'mesa_id',
        'status_id',
        'garcon_id',
        'admin_id',
        'data_abertura',
        'hora_abertura',
        'data_fechamento',
        'hora_fechamento',
        'identificacao',
        'tipo_pedido',
        'total',
        'tipo_pedido'
    ];

    public function cliente(){
        return $this->belongsTo(ComandaCliente::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }

    public function garcon(){
        return $this->belongsTo(ComandaGarcon::class);
    }

    public function admin(){
        return $this->belongsTo(ComandaAdmin::class);
    }

    public function mesa(){
        return $this->belongsTo(Mesa::class);
    }

    public function comanda(){
        return $this->belongsTo(Comanda::class);
    }

    public function itens(){
        return $this->hasMany(ComandaItemPedido::class, 'pedido_id', 'id');
    }

    public static function somarTotal($id){
        $pedido             = ComandaPedido::find($id);
        if($pedido){
            $pedido->total      = ComandaItemPedido::where("pedido_id", $id)->sum("subtotal");
            $pedido->save();
        }
    }
}
