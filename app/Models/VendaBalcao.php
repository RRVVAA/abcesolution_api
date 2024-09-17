<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Tenant\Traits\EmpresaTrait;

class VendaBalcao extends Model
{
    use HasFactory;
    protected $fillable = [
        'empresa_id', 'cliente_id', 'vendedor_id', 'usuario_id','data_venda','valor_venda','valor_frete','desconto_valor',
        'desconto_per', 'valor_desconto', 'valor_liquido', 'observacao','status_id'
    ];
    

    public function status(){
        return $this->belongsTo(Status::class, 'status_id');
    }
    
    public function vendedor(){
        return $this->belongsTo(Vendedor::class, 'vendedor_id');
    }
    public function itens(){
        return $this->hasMany(ItemVendaBalcao::class, 'venda_balcao_id', 'id');
    }
    
    public static function somarTotal($id){
        $venda          = VendaBalcao::find($id);
        $valor_venda    = ItemVendaBalcao::where("venda_balcao_id", $id)->sum("subtotal_liquido");
        $venda->valor_desconto = 0;
        if($venda->desconto_valor > 0){
            $venda->valor_desconto = $venda->desconto_valor;
        }
        if($venda->desconto_per > 0){
            $venda->valor_desconto = $venda->desconto_per * $valor_venda * 0.01;
        }
        
        $venda->valor_venda = $valor_venda;
        $venda->valor_liquido = $valor_venda +  $venda->valor_frete - $venda->valor_desconto;
        $venda->save();
    }
}
