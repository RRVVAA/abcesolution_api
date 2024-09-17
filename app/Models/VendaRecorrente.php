<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendaRecorrente extends Model
{
    use HasFactory;
    protected $fillable = [
        'empresa_id', 'produto_recorrente_id', 'vendedor_id', 'cliente_id', 'tipo_cobranca_id','status_id',
        'status_financeiro_id','valor_total','valor_primeira_parcela','valor_recorrente','data_contrato','primeiro_vencimento'
    ];
    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    

    
    public function status(){
        return $this->belongsTo(Status::class, 'status_id');
    }
    
    public function status_financeiro(){
        return $this->belongsTo(Status::class, 'status_financeiro_id');
    }
}
