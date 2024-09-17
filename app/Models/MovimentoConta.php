<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovimentoConta extends Model
{
    use HasFactory;
    protected $fillable =[
        'empresa_id',
        'sangria_id',        
        'suplemento_id',
        'recebimento_id',
        'conta_id',
        'documento',
        'data_emissao',
        'data_compensacao',
        'tipo_movimento',
        'historico',
        'valor',
        'usuario_id',
        'origem',
        'classificacao_financeira_id',
    ];
    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    
    
    public function conta(){
        return $this->belongsTo(ContaCorrente::class, 'conta_id');
    }
    
    public function classificacaoFinanceira(){
        return $this->belongsTo(ClassificacaoFinanceira::class, 'classificacao_financeira_id');
    }
}
