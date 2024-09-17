<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/nfe/transmitir',
        '/nfe/consultar',
        '/nfe/cancelar',
        '/nfe/correcao',
        '/nfe/cancelar_e_imprimir',
        '/nfe/imprimircancelado',
        '/nfe/email',
        '/nfe/inutilizar',
        
        '/nfce/transmitir',
        '/nfce/consultar',
        '/nfce/cancelar',
        '/nfce/correcao',
        '/nfce/cancelar_e_imprimir',
        '/nfce/imprimircancelado',
        '/nfce/email',
        '/nfce/inutilizar',
        
        '/lojavirtual/salvarCliente',
        '/lojavirtual/atualizarDadosCliente',
        '/lojavirtual/salvarEnderecoCliente',
        '/lojavirtual/fazerLogin',        
        '/lojavirtual/novoPedido', 
        '/lojavirtual/addItem', 
        '/lojavirtual/atualizarPedido',         
        '/lojavirtual/pagarPorTransferencia',
        '/lojavirtual/pagarPorBoleto',
        '/lojavirtual/pagarPorCartao',
        '/lojavirtual/pagarPedido',
        
        '/pdv/abrirCaixa',
        '/pdv/salvarVenda',
        '/pdv/salvarNfcePelaVenda',
        '/pdv/getDadosParaGerarNfce'

    ];
}
