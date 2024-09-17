<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PdvVenda extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'empresa_id',
        'cliente_id',
        'caixa_id',
        'usuario_id',
        'data_venda',
        'xml_path',
        'chave',
        'nfce',
        'numero_emissao',
        'forma_pagamento',
        'valor_venda',
        'valor_desconto',
        'valor_liquido',
        'qtde_parcela',
        'status_id',
        'desconto_per',
        'desconto_valor',
        'estornou_estoque',
        'acrescimo_valor',
        'acrescimo_per',
        'valor_acrescimo',
        'cliente_cnpj',
        'cliente_cpf',
        'titulo',
        'vendedor_id',
        'venda_balcao_id',
        'venda_loja_id',
        'orcamento_id',
        'venda_id',
        'pedido_cliente_id',
        'classificacao_financeira_id',
        'valor_liquido',
        'uuid',
        'pdvvenda_id',
        'transacao_id',
        'cliente_id',
        'cliente_consumidor_id',
        'cupom_desconto_id'
    ];
    public function itens()
    {
        return $this->hasMany(PdvItemVenda::class, 'venda_id', 'id');
    }
    public function pagamentos()
    {
        return $this->hasMany(PdvPagamento::class, 'venda_id', 'id');
    }
    public function duplicatas()
    {
        return $this->hasMany(PdvDuplicata::class, 'venda_id', 'id');
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    public function cupom()
    {
        return $this->belongsTo(CupomDesconto::class, 'cupom_desconto_id');
    }
    public function caixa()
    {
        return $this->belongsTo(PdvCaixa::class, 'caixa_id');
    }
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    public static function somarTotal($id)
    {
        $venda = PdvVenda::find($id);
        $valor_total = PdvItemVenda::where("venda_id", $id)->sum("subtotal_liquido");
        $venda->valor_desconto = 0;
        $venda->valor_acrescimo = 0;

        if ($venda->desconto_valor > 0) {
            $venda->valor_desconto = $venda->desconto_valor;
            $venda->desconto_per = 0;
        }

        if ($venda->desconto_per > 0) {
            $venda->valor_desconto = $venda->desconto_per * $valor_total * 0.01;
            $venda->desconto_valor > 0;
        }

        if ($venda->acrescimo_valor > 0) {
            $venda->valor_acrescimo = $venda->acrescimo_valor;
            $venda->acrescimo_per > 0;
        }

        if ($venda->acrescimo_per > 0) {
            $venda->valor_acrescimo = $venda->acrescimo_per * $valor_total * 0.01;
            $venda->acrescimo_valor > 0;
        }

        $venda->valor_venda = $valor_total;
        $venda->valor_liquido = $valor_total - $venda->valor_desconto + $venda->valor_acrescimo;
        $venda->save();
    }
}
