<?php

use App\Http\Controllers\CategoriaApiController;
use App\Http\Controllers\ClienteApiController;
use App\Http\Controllers\CobrancaApiController;
use App\Http\Controllers\DeliveryApiController;
use App\Http\Controllers\EmpresaApiController;
use App\Http\Controllers\FaturaWebHookController;
use App\Http\Controllers\FrenteApiController;
use App\Http\Controllers\LojaApiController;
use App\Http\Controllers\LojaPedidoApiController;
use App\Http\Controllers\LojaVirtualController;
use App\Http\Controllers\LojaWebHookController;
use App\Http\Controllers\MercadoPagoApiController;
use App\Http\Controllers\NfceController;
use App\Http\Controllers\NfeController;
use App\Http\Controllers\PdvApiController;
use App\Http\Controllers\PdvCaixaApiController;
use App\Http\Controllers\PdvController;
use App\Http\Controllers\PdvNumeroApiController;
use App\Http\Controllers\PdvResgateApiController;
use App\Http\Controllers\PdvSangriaApiController;
use App\Http\Controllers\PdvSuplementoApiController;
use App\Http\Controllers\PdvVendaApiController;
use App\Http\Controllers\PedidoClienteApiController;
use App\Http\Controllers\ProdutoApiController;
use App\Http\Controllers\TesteController;
use App\Http\Controllers\VendaBalcaoApiController;
use App\Http\Controllers\WebHookController;
use App\Http\Controllers\Acl\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//NOVO API
Route::post('/login/logarPdv', [LoginController::class, 'logarPdv']);
Route::post('/login/logarLoja', [LoginController::class, 'logarLoja']);
Route::post('/login/logarBalcao', [LoginController::class, 'logarBalcao']);

Route::get('testeapi', [TesteController::class, 'testeapi']);
Route::post('teste/vendapix', [TesteController::class, 'vendapix']);
Route::post('lojawebhook/pix', [LojaWebHookController::class, 'pix']);

//Empresa
Route::get('/empresa', [EmpresaApiController::class, 'index']);
Route::get('/empresa/{uuid}', [EmpresaApiController::class, 'show']);

//Categoria
Route::get('/categoria/lista/{token}', [CategoriaApiController::class, 'categoriasPorEmpresa']);
Route::get('/categoria/{uuid}', [CategoriaApiController::class, 'show']);

//Produto
Route::get('/produto/produtosPorEmpresa/{token}', [ProdutoApiController::class, 'produtosPorEmpresa']);
Route::get('/produto/{uuid}', [ProdutoApiController::class, 'show']);
Route::get('/produto/pesquisaPorNome/{nome}/{token}', [ProdutoApiController::class, 'pesquisaPorNome']);
Route::get('/produto/produtoPorCodigo/{codigo}/{token}', [ProdutoApiController::class, 'produtoPorCodigo']);
Route::get('/produto/produtoPorCodigoBarra/{barra}/{token}', [ProdutoApiController::class, 'produtoPorCodigoBarra']);
Route::get('/produto/produtoPorIdOuCodigoBarra/{barra}/{token}', [ProdutoApiController::class, 'produtoPorIdOuCodigoBarra']);
Route::get('/produto/detalheProduto/{codigo}/{token}', [ProdutoApiController::class, 'detalheProduto']);


//Cliente
Route::get('/cliente', [ClienteApiController::class, 'categoriasPorEmpresa']);
Route::post('/cliente/logar', [ClienteApiController::class, 'logar']);

//Pedido
Route::post('/pedidocliente', [PedidoClienteApiController::class, 'store']);
Route::post('/pedidocliente/filtro', [PedidoClienteApiController::class, 'filtro']);
Route::get('/pedidocliente/{identificador}', [PedidoClienteApiController::class, 'show']);
Route::get('/pedidocliente/cobrancas/{id}', [PedidoClienteApiController::class, 'cobrancas']);

//Cobranca
Route::get('/cobranca/lista/{identificador}', [CobrancaApiController::class, 'lista']);
Route::get('/cobranca/detalhe/{uuid}', [CobrancaApiController::class, 'detalhe']);

//Loja Virtual
Route::post('/loja/home', [LojaApiController::class, 'home']);
Route::post('/loja/finalizarPedido', [LojaApiController::class, 'finalizarPedido']);
Route::post('/loja/atualizarItem', [LojaApiController::class, 'atualizarItem']);
Route::post('/loja/excluirItem', [LojaApiController::class, 'excluirItem']);
Route::get('/loja/getEnderecoPeloId/{id}', [LojaApiController::class, 'getEnderecoPeloId']);
Route::get('/loja/categoria/{uuid}/{id}', [LojaApiController::class, 'categoria']);
Route::get('/loja/subcategoria/{uuid}/{id}', [LojaApiController::class, 'subcategoria']);
Route::get('/loja/detalhe/{uuid}', [LojaApiController::class, 'detalhe']);
Route::get('/loja/getPedidoPeloUuid/{uuid}', [LojaApiController::class, 'getPedidoPeloUuid']);
Route::get('/loja/pesquisa/{uuid}/{id}', [LojaApiController::class, 'pesquisa']);
Route::post('/loja/novoPedido', [LojaApiController::class, 'novoPedido']);
Route::post('/loja/novoCliente', [LojaApiController::class, 'novoCliente']);
Route::post('/loja/salvarEnderecoCliente', [LojaApiController::class, 'salvarEnderecoCliente']);
Route::post('/loja/setarEnderecoFrete', [LojaApiController::class, 'setarEnderecoFrete']);
Route::post('/loja/logar', [LojaApiController::class, 'logar']);
Route::post('/loja/addItem', [LojaApiController::class, 'addItem']);
Route::post('/loja/carrinho', [LojaApiController::class, 'carrinho']);
Route::post('/loja/perfil', [LojaApiController::class, 'perfil']);
Route::post('/loja/aplicarCupom', [LojaApiController::class, 'aplicarCupom']);
Route::get('/loja/excluirCupom/{id}', [LojaApiController::class, 'excluirCupom']);
Route::post('/loja/retormarPedidoDaLojaPeloPdv', [LojaApiController::class, 'retormarPedidoDaLojaPeloPdv']);

//Cardápio 
Route::post('/delivery/home', [DeliveryApiController::class, 'home']);
Route::post('/delivery/addItem', [DeliveryApiController::class, 'addItem']);
Route::post('/delivery/novoPedido', [DeliveryApiController::class, 'novoPedido']);
Route::post('/delivery/novoCliente', [DeliveryApiController::class, 'novoCliente']);
Route::post('/delivery/salvarEnderecoCliente', [DeliveryApiController::class, 'salvarEnderecoCliente']);

Route::post('/lojapedido', [LojaPedidoApiController::class, 'store']);
Route::get('/lojapedido/finalizar/{id}', [LojaPedidoApiController::class, 'finalizar']);

//FRENTE
Route::post('/frente/home',[FrenteApiController::class, 'home']);


//Pdv
Route::post('/pdv/aplicarCupom',[PdvApiController::class, 'aplicarCupom']);
Route::post('/pdv/excluirCupom',[PdvApiController::class, 'excluirCupom']);
Route::post('/pdv/armazenarVenda',[PdvApiController::class, 'armazenarVenda']);
Route::post('/pdv/gerarCrediario',[PdvApiController::class, 'gerarCrediario']);
Route::post('/pdv/gerarPagamentoCartao',[PdvApiController::class, 'gerarPagamentoCartao']);
Route::post('/pdv/inserirItem',[PdvApiController::class, 'inserirItem']);
Route::post('/pdv/buscaCliente',[PdvApiController::class, 'buscaCliente']);
Route::post('/pdv/vincularCliente',[PdvApiController::class, 'vincularCliente']);
Route::post('/pdv/home',[PdvApiController::class, 'home']);
Route::post('/pdv/abrirCaixa',[PdvApiController::class, 'abrirCaixa']);
Route::post('/pdv/fecharCaixa',[PdvApiController::class, 'fecharCaixa']);
Route::get('/pdv/verificarRequisitos/{id}',[PdvApiController::class, 'verificarRequisitos'])->name('pdv.verificarRequisitos');

//PDV CAIXA
Route::get('/pdvcaixa/listaCaixaAbertoPorUsuario/{uuid}',[PdvCaixaApiController::class, 'listaCaixaAbertoPorUsuario']);
Route::get('/pdvcaixa/verificaSeTemCaixaAbertoPorUsuario/{uuid}',[PdvCaixaApiController::class, 'verificaSeTemCaixaAbertoPorUsuario']);
Route::post('/pdvcaixa/abrir',[PdvCaixaApiController::class, 'abrir']);
Route::get('/pdvcaixa/getCaixa/{id}',[PdvCaixaApiController::class, 'getCaixa']);
Route::get('/pdvcaixa/detalhamento/{id}',[PdvCaixaApiController::class, 'detalhamento']);
Route::get('/pdvcaixa/fechar/{id}/{id_usuario}',[PdvCaixaApiController::class, 'fechar']);

//PdvNumero
Route::get('/pdvnumero/lista/{token}',[PdvNumeroApiController::class, 'lista']);

Route::post('webhook/pix', [WebHookController::class, 'pix']);
Route::post('webhook/escuta', [WebHookController::class, 'escuta']);

//Mercado Pago

Route::get('/mercadopago/buscarTransacaoMercadoPago/{id}',[MercadoPagoApiController::class, 'buscarTransacaoMercadoPago']);
Route::get('/mercadopago/verificaSeCobrancaPagaNoPix/{id}',[MercadoPagoApiController::class, 'verificaSeCobrancaPagaNoPix']);
Route::get('/mercadopago/verificaSePedidoPagoNoPix/{id}',[MercadoPagoApiController::class, 'verificaSePedidoPagoNoPix']);
Route::get('/mercadopago/verificaSeFaturaPagaNoPix/{id}',[MercadoPagoApiController::class, 'verificaSeFaturaPagaNoPix']);
Route::get('/mercadopago/verificaPagamentoPix/{id}',[MercadoPagoApiController::class, 'verificaPagamentoPix']);
Route::post("/mercadopago/pix",[MercadoPagoApiController::class,"pix"])->name("mercadopago.pix");
Route::post("/mercadopago/cartao",[MercadoPagoApiController::class,"cartao"])->name("mercadopago.cartao");
Route::post("/mercadopago/boleto",[MercadoPagoApiController::class,"boleto"])->name("mercadopago.boleto");

//PdvSangria
Route::post('/pdvsangria/salvar',[PdvSangriaApiController::class, 'salvar']);
Route::get('/pdvsangria/listaPorCaixa/{id}',[PdvSangriaApiController::class, 'listaPorCaixa']);
Route::get('/pdvsangria/listaPorUsuario/{uuid}',[PdvSangriaApiController::class, 'listaPorUsuario']);

//PdvSuplementoa
Route::post('/pdvsuplemento/salvar',[PdvSuplementoApiController::class, 'salvar']);
Route::get('/pdvsuplemento/listaPorCaixa/{id}',[PdvSuplementoApiController::class, 'listaPorCaixa']);
Route::get('/pdvsuplemento/listaPorUsuario/{uuid}',[PdvSuplementoApiController::class, 'listaPorUsuario']);


//Venda Balcão
Route::post('/vendabalcao/novoPedido',[VendaBalcaoApiController::class, 'novoPedido']);
Route::post('/vendabalcao/inserirItem',[VendaBalcaoApiController::class, 'inserirItem']);
Route::post('/vendabalcao/excluirItem',[VendaBalcaoApiController::class, 'excluirItem']);
Route::get('/vendabalcao/buscarPedido/{id}',[VendaBalcaoApiController::class, 'buscarPedido']);
Route::get('/vendabalcao/buscarOs/{id}',[VendaBalcaoApiController::class, 'buscarOs']);
Route::post('/vendabalcao/finalizarPedido',[VendaBalcaoApiController::class, 'finalizarPedido']);
Route::post('/vendabalcao/cancelarPedido',[VendaBalcaoApiController::class, 'cancelarPedido']);
Route::post('/vendabalcao/excluirBalcao',[VendaBalcaoApiController::class, 'excluirBalcao']);

//Importação para PDV Venda
Route::post('/resgate', [PdvResgateApiController::class, 'resgatar']);
Route::post('/resgate/criarPdvVendaPeloBalcao',[PdvResgateApiController::class, 'criarPdvVendaPeloBalcao']);
Route::post('/resgate/criarPdvVendaPelaLoja',[PdvResgateApiController::class, 'criarPdvVendaPelaLoja']);
Route::post('/resgate/criarPdvVendaPeloOrcamento',[PdvResgateApiController::class, 'criarPdvVendaPeloOrcamento']);
Route::post('/resgate/criarPdvVendaPeloPedidoCliente',[PdvResgateApiController::class, 'criarPdvVendaPeloPedidoCliente']);
Route::post('/resgate/criarPdvVendaPelVenda',[PdvResgateApiController::class, 'criarPdvVendaPelVenda']);
Route::post('/resgate/criarPdvVendaPelOrdemServico',[PdvResgateApiController::class, 'criarPdvVendaPelOrdemServico']);
Route::get('/resgate/lista/{uuid}',[PdvResgateApiController::class, 'lista']);


Route::get('/pdvvenda/getVendaAbertaPorUsuario/{uuid}/{caixa_id}',[PdvVendaApiController::class, 'getVendaAbertaPorUsuario']);
Route::get('/pdvvenda/iniciarPdvVenda/{uuid}',[PdvVendaApiController::class, 'iniciarPdvVenda']);
Route::post('/pdvvenda/salvar',[PdvVendaApiController::class, 'salvar']);
Route::post('/pdvvenda/finalizarVenda',[PdvVendaApiController::class, 'finalizarVenda']);
Route::post('/pdvvenda/cancelarVenda',[PdvVendaApiController::class, 'cancelarVenda']);
Route::post('/pdvvenda/salvarItens',[PdvVendaApiController::class, 'salvarItens']);
Route::post('/pdvvenda/inserirItem',[PdvVendaApiController::class, 'inserirItem']);
Route::get('/pdvvenda/excluirItem/{id}/{idVenda}',[PdvVendaApiController::class, 'excluirItem']);
Route::get('/pdvvenda/gerarNfcePelaVenda/{id}',[PdvVendaApiController::class, 'gerarNfcePelaVenda']);
Route::post('/pdvvenda/salvarPagamento',[PdvVendaApiController::class, 'salvarPagamento']);
Route::post('/pdvvenda/enviarDescontoAcrescimento',[PdvVendaApiController::class, 'enviarDescontoAcrescimento']);
Route::get('/pdvvenda/listaVendaPorUsuario/{id_empresa}/{id_usuario}',[PdvVendaApiController::class, 'listaVendaPorUsuario']);
Route::get('/pdvvenda/listaVendaPorCaixa/{id_empresa}/{id_caixa}',[PdvVendaApiController::class, 'listaVendaPorCaixa']);
Route::get('/pdvvenda/excluirDuplicata/{id}/{idVenda}',[PdvVendaApiController::class, 'excluirDuplicata']);
Route::get('/pdvvenda/listaPorCaixa/{id}',[PdvVendaApiController::class, 'listaPorCaixa']);
Route::get('/pdvvenda/listaPorUsuario/{uuid}',[PdvVendaApiController::class, 'listaPorUsuario']);
Route::get('/pdvvenda/getVendaPorId/{id}',[PdvVendaApiController::class, 'getVendaPorId']);


//NFCE

Route::get('/nfce/transmitirNfce/{id}',[NfceController::class, 'transmitirNfce'])->name('nfce.transmitirNfce');
Route::get('/nfce/transmitirPelaComanda/{id}',[NfceController::class, 'transmitirPelaComanda'])->name('nfce.transmitirPelaComanda');

Route::get('/nfce/transmitirPelaNfce/{id}',[PdvController::class, 'transmitirPelaNfce'])->name('nfce.transmitirPelaNfce');
Route::get('/nfce/imprimirDanfcePelaVenda/{id}',[NfceController::class, 'imprimirDanfcePelaVenda']);
Route::get('/nfce/gerarNfcePelaVenda/{id}',[NfceController::class, 'gerarNfcePelaVenda']);
Route::get('/nfce/transmitirPelaVenda/{id}',[NfceController::class, 'transmitirPelaVenda'])->name('nfce.transmitirPelaVenda');
Route::post('/nfce/transmitir',[NfceController::class, 'transmitir']);
Route::get('/nfce/xml/{chave}',[NfceController::class, 'xml']);
Route::get('/nfce/baixarXml/{chave}',[NfceController::class, 'baixarXml']);
Route::get('/nfce/baixarPdf/{chave}',[NfceController::class, 'baixarPdf']);
Route::get('/nfce/danfce/{chave}',[NfceController::class, 'danfce']);
Route::post('/nfce/email',[NfceController::class, 'email']);
Route::post('/nfce/cancelarNfce',[NfceController::class, 'cancelarNfce']);
Route::get('/nfce/imprimircancelado/{id}',[NfceController::class, 'imprimircancelado']);

//NFCE
Route::get('/pdv/transmitirPelaVenda/{id}',[PdvController::class, 'transmitirPelaVenda'])->name('pdv.transmitirPelaVenda');
Route::get('/pdv/transmitirPelaNfce/{id}',[PdvController::class, 'transmitirPelaNfce'])->name('pdv.transmitirPelaNfce');
Route::get('/pdv/transmitir/{id}',[PdvController::class, 'transmitir'])->name('pdv.transmitir');
Route::get('/pdv/listaCaixa/{id_empresa}/{id_usuario}',[PdvController::class, 'listaCaixa'])->name('pdv.listaCaixa');

Route::get('/pdv/listaSangriaPorUsuario/{id_empresa}/{id_usuario}',[PdvController::class, 'listaSangriaPorUsuario'])->name('pdv.listaSangriaPorUsuario');
Route::get('/pdv/listaSangriaPorCaixa/{id_empresa}/{id_caixa}',[PdvController::class, 'listaSangriaPorCaixa'])->name('pdv.listaSangriaPorCaixa');
Route::post('/pdv/salvarSangria',[PdvController::class, 'salvarSangria'])->name('pdv.salvarSangria');

Route::get('/pdv/listaSuplementoPorUsuario/{id_empresa}/{id_usuario}',[PdvController::class, 'listaSuplementoPorUsuario'])->name('pdv.listaSuplementoPorUsuario');
Route::get('/pdv/listaSuplementoPorCaixa/{id_empresa}/{id_caixa}',[PdvController::class, 'listaSuplementoPorCaixa'])->name('pdv.listaSuplementoPorCaixa');
Route::post('/pdv/salvarSuplemento',[PdvController::class, 'salvarSuplemento'])->name('pdv.salvarSuplemento');

Route::get('/pdv/caixaDetalhes/{id}/{id_empresa}',[PdvController::class, 'caixaDetalhes'])->name('pdv.caixaDetalhes');
Route::post('/pdv/salvarVenda',[PdvController::class, 'salvarVenda'])->name('pdv.salvarVenda');
Route::get('/pdv/salvar_nfce_e_transmitir/{id}/{id_empresa}',[PdvController::class, 'salvar_nfce_e_transmitir'])->name('pdv.salvar_nfce_e_transmitir');
Route::get('/pdv/getDadosParaGerarNfcePelaVenda/{id}/{id_empresa}',[PdvController::class, 'getDadosParaGerarNfcePelaVenda'])->name('pdv.getDadosParaGerarNfcePelaVenda');
Route::post('/pdv/salvarNfcePelaVenda',[PdvController::class, 'salvarNfcePelaVenda'])->name('pdv.salvarNfcePelaVenda');
Route::post('/pdv/getDadosParaGerarNfce',[PdvController::class, 'getDadosParaGerarNfce'])->name('pdv.getDadosParaGerarNfce');
Route::get('/pdv/imprimirNfcePelaVenda/{id}',[PdvController::class, 'imprimirNfcePelaVenda'])->name('pdv.imprimirNfcePelaVenda');
Route::get('/pdv/imprimirNfcePeloNfce/{id}',[PdvController::class, 'imprimirNfcePeloNfce'])->name('pdv.imprimirNfcePeloNfce');
Route::get('/pdv/imprimirNfcePelaChave/{id}',[PdvController::class, 'imprimirNfcePelaChave'])->name('pdv.imprimirNfcePelaChave');

Route::get('lojawebhook/pagamento/{id}', [LojaWebHookController::class, 'obterPagamento']);
Route::post('lojawebhook/cartao', [LojaWebHookController::class, 'cartao']);
Route::post('lojawebhook/boleto', [LojaWebHookController::class, 'boleto']);

Route::post('faturawebhook/pix', [FaturaWebHookController::class, 'pix']);
Route::get('faturawebhook/pagamento/{id}', [FaturaWebHookController::class, 'obterPagamento']);
Route::post('faturawebhook/cartao', [FaturaWebHookController::class, 'cartao']);
Route::post('faturawebhook/boleto', [FaturaWebHookController::class, 'boleto']);
Route::get('faturawebhook/testePagamento/{id}', [FaturaWebHookController::class, 'testePagamento']);

//NFE
Route::post('/nfe/transmitir',[NfeController::class, 'transmitir']);
Route::get('/nfe/xml/{chave}',[NfeController::class, 'xml']);
Route::get('/nfe/lerXml/{chave}',[NfeController::class, 'lerXml']);
Route::get('/nfe/baixarXml/{chave}',[NfeController::class, 'baixarXml']);
Route::get('/nfe/baixarPdf/{chave}',[NfeController::class, 'baixarPdf']);
Route::get('/nfe/danfe/{chave}',[NfeController::class, 'danfe']);
Route::get('/nfe/imprimirDanfePelaChave/{id}',[NfeController::class, 'imprimirDanfePelaChave']);
Route::get('/nfe/imprimirDanfePelaNfe/{id}',[NfeController::class, 'imprimirDanfePelaNfe']);
Route::get('/nfe/imprimirDanfePelaVenda/{id}',[NfeController::class, 'imprimirDanfePelaVenda']);
Route::get('/nfe/simularDanfe/{id}',[NfeController::class, 'simularDanfe']);
Route::get('/nfe/transmitirPelaNfe/{id}',[NfeController::class, 'transmitirPelaNfe']);
Route::post('/nfe/email',[NfeController::class, 'email']);
Route::post('/nfe/cartaCorrecao',[NfeController::class, 'cartaCorrecao']);
Route::post('/nfe/inutilizarNfe',[NfeController::class, 'inutilizarNfe']);
Route::get('/nfe/imprimirCce/{id}',[NfeController::class, 'imprimirCce']);
Route::get('/nfe/consultarNfe/{id}',[NfeController::class, 'consultarNfe']);
Route::post('/nfe/cancelarNfe',[NfeController::class, 'cancelarNfe']);
Route::get('/nfe/imprimircancelado/{id}',[NfeController::class, 'imprimircancelado']);

//LOJA VIRTUAL
Route::get('/lojavirtual/listaProduto/{id}',[LojaVirtualController::class, 'listaProduto'])->name('loja.listaProduto');
Route::get('/lojavirtual/getProdutosSemelhantes/{id}/{id_empresa}',[LojaVirtualController::class, 'getProdutosSemelhantes'])->name('loja.getProdutosSemelhantes');
Route::get('/lojavirtual/getProduto/{id}',[LojaVirtualController::class, 'getProduto'])->name('loja.getProduto');
Route::get('/lojavirtual/getConfiguracao/{id}',[LojaVirtualController::class, 'getConfiguracao'])->name('loja.getConfiguracao');
Route::get('/lojavirtual/listaBanner/{id}',[LojaVirtualController::class, 'listaBanner'])->name('loja.listaBanner');
Route::get('/lojavirtual/listaCategoria/{id}',[LojaVirtualController::class, 'listaCategoria'])->name('loja.listaCategoria');
Route::post('/lojavirtual/salvarCliente',[LojaVirtualController::class, 'salvarCliente'])->name('loja.salvarCliente');
Route::post('/lojavirtual/fazerLogin',[LojaVirtualController::class, 'fazerLogin'])->name('loja.fazerLogin');
Route::get('/lojavirtual/getCliente/{id}',[LojaVirtualController::class, 'getCliente'])->name('loja.getCliente');
Route::post('/lojavirtual/atualizarDadosCliente',[LojaVirtualController::class, 'atualizarDadosCliente'])->name('loja.atualizarDadosCliente');
Route::post('/lojavirtual/atualizarPedido',[LojaVirtualController::class, 'atualizarPedido'])->name('loja.atualizarPedido');
Route::post('/lojavirtual/pagarPedido',[LojaVirtualController::class, 'pagarPedido'])->name('loja.pagarPedido');
Route::post('/lojavirtual/salvarEnderecoCliente',[LojaVirtualController::class, 'salvarEnderecoCliente'])->name('loja.salvarEnderecoCliente');
Route::get('/lojavirtual/getEnderecoCliente/{id}',[LojaVirtualController::class, 'getEnderecoCliente'])->name('loja.getEnderecoCliente');
Route::get('/lojavirtual/getPedidoAleatorio/{id}',[LojaVirtualController::class, 'getPedidoAleatorio'])->name('loja.getPedidoAleatorio');
Route::get('/lojavirtual/getPedidoNovoDoCliente/{id}',[LojaVirtualController::class, 'getPedidoNovoDoCliente'])->name('loja.getPedidoNovoDoCliente');
Route::get('/lojavirtual/getPedidoPorPedidoCliente/{id}/{idcliente}',[LojaVirtualController::class, 'getPedidoPorPedidoCliente'])->name('loja.getPedidoPorPedidoCliente');
Route::get('/lojavirtual/excluirItemCarrinho/{id}',[LojaVirtualController::class, 'excluirItemCarrinho'])->name('loja.excluirItemCarrinho');
Route::get('/lojavirtual/verificaSePedidoTemVenda/{id}',[LojaVirtualController::class, 'verificaSePedidoTemVenda'])->name('loja.verificaSePedidoTemVenda');

Route::post('/lojavirtual/pagarPorTransferencia',[LojaVirtualController::class, 'pagarPorTransferencia'])->name('loja.pagarPorTransferencia');
Route::post('/lojavirtual/pagarPorBoleto',[LojaVirtualController::class, 'pagarPorBoleto'])->name('loja.pagarPorBoleto');
Route::post('/lojavirtual/pagarPorCartao',[LojaVirtualController::class, 'pagarPorCartao'])->name('loja.pagarPorCartao');

Route::post('/lojavirtual/novoPedido',[LojaVirtualController::class, 'novoPedido'])->name('loja.novoPedido');
Route::post('/lojavirtual/addItem',[LojaVirtualController::class, 'addItem'])->name('loja.addItem');

//PDV
/*Route::get('/pdv/verificarRequisito/{id}',[PdvController::class, 'verificarRequisito'])->name('pdv.verificarRequisito');
Route::get('/pdv/transmitirPelaVenda/{id}',[PdvController::class, 'transmitirPelaVenda'])->name('pdv.transmitirPelaVenda');
Route::get('/pdv/transmitirPelaNfce/{id}',[PdvController::class, 'transmitirPelaNfce'])->name('pdv.transmitirPelaNfce');
Route::get('/pdv/transmitir/{id}',[PdvController::class, 'transmitir'])->name('pdv.transmitir');
Route::get('/pdv/listaNumeroCaixa/{id}',[PdvController::class, 'listaNumeroCaixa'])->name('pdv.listaNumeroCaixa');
Route::get('/pdv/listaCaixa/{id_empresa}/{id_usuario}',[PdvController::class, 'listaCaixa'])->name('pdv.listaCaixa');

Route::get('/pdv/listaSangriaPorUsuario/{id_empresa}/{id_usuario}',[PdvController::class, 'listaSangriaPorUsuario'])->name('pdv.listaSangriaPorUsuario');
Route::get('/pdv/listaSangriaPorCaixa/{id_empresa}/{id_caixa}',[PdvController::class, 'listaSangriaPorCaixa'])->name('pdv.listaSangriaPorCaixa');
Route::post('/pdv/salvarSangria',[PdvController::class, 'salvarSangria'])->name('pdv.salvarSangria');

Route::get('/pdv/listaSuplementoPorUsuario/{id_empresa}/{id_usuario}',[PdvController::class, 'listaSuplementoPorUsuario'])->name('pdv.listaSuplementoPorUsuario');
Route::get('/pdv/listaSuplementoPorCaixa/{id_empresa}/{id_caixa}',[PdvController::class, 'listaSuplementoPorCaixa'])->name('pdv.listaSuplementoPorCaixa');
Route::post('/pdv/salvarSuplemento',[PdvController::class, 'salvarSuplemento'])->name('pdv.salvarSuplemento');

Route::get('/pdv/listaVendaPorUsuario/{id_empresa}/{id_usuario}',[PdvController::class, 'listaVendaPorUsuario'])->name('pdv.listaVendaPorUsuario');
Route::get('/pdv/listaVendaPorCaixa/{id_empresa}/{id_caixa}',[PdvController::class, 'listaVendaPorCaixa'])->name('pdv.listaVendaPorCaixa');
Route::get('/pdv/getVenda/{id_empresa}/{id_venda}',[PdvController::class, 'getVenda'])->name('pdv.getVenda');


Route::get('/pdv/listaCaixaAbertoPorUsuario/{id}/{id_empresa}',[PdvController::class, 'listaCaixaAbertoPorUsuario'])->name('pdv.listaCaixaAbertoPorUsuario');

Route::get('/pdv/verificaSeTemCaixaAbertoPorUsuario/{id}/{id_empresa}',[PdvController::class, 'verificaSeTemCaixaAbertoPorUsuario'])->name('pdv.verificaSeTemCaixaAbertoPorUsuario');
Route::get('/pdv/caixaDetalhes/{id}/{id_empresa}',[PdvController::class, 'caixaDetalhes'])->name('pdv.caixaDetalhes');
Route::post('/pdv/abrirCaixa',[PdvController::class, 'abrirCaixa'])->name('pdv.abrirCaixa');
Route::post('/pdv/salvarVenda',[PdvController::class, 'salvarVenda'])->name('pdv.salvarVenda');
Route::get('/pdv/salvar_nfce_e_transmitir/{id}/{id_empresa}',[PdvController::class, 'salvar_nfce_e_transmitir'])->name('pdv.salvar_nfce_e_transmitir');
Route::get('/pdv/getDadosParaGerarNfcePelaVenda/{id}/{id_empresa}',[PdvController::class, 'getDadosParaGerarNfcePelaVenda'])->name('pdv.getDadosParaGerarNfcePelaVenda');
Route::post('/pdv/salvarNfcePelaVenda',[PdvController::class, 'salvarNfcePelaVenda'])->name('pdv.salvarNfcePelaVenda');
Route::post('/pdv/getDadosParaGerarNfce',[PdvController::class, 'getDadosParaGerarNfce'])->name('pdv.getDadosParaGerarNfce');
Route::get('/pdv/imprimirNfcePelaVenda/{id}',[PdvController::class, 'imprimirNfcePelaVenda'])->name('pdv.imprimirNfcePelaVenda');
Route::get('/pdv/imprimirNfcePeloNfce/{id}',[PdvController::class, 'imprimirNfcePeloNfce'])->name('pdv.imprimirNfcePeloNfce');
Route::get('/pdv/imprimirNfcePelaChave/{id}',[PdvController::class, 'imprimirNfcePelaChave'])->name('pdv.imprimirNfcePelaChave');

*/




Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
