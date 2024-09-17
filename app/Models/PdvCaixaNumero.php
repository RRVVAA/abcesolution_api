<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PdvCaixaNumero extends Model
{
    use HasFactory;
    protected $fillable = ["empresa_id", "num_caixa","descricao","padrao_busca",'gerar_nfce',
        'transmitir_nfce', 'imprimir_direto_na_impressora','mostrar_pdf',
        'gerar_financeiro','gerar_estoque','pergunta_antes_de_finalizar_venda','apos_a_venda'];
    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
