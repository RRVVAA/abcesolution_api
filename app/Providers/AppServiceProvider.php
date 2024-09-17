<?php

namespace App\Providers;

use App\Models\Cliente;
use App\Models\FinContaReceber;
use App\Models\FinRecebimento;
use App\Models\ItemVendaBalcao;
use App\Models\LojaItemPedido;
use App\Models\LojaPedido;
use App\Models\MercadoPagoTransacao;
use App\Models\Movimento;
use App\Models\NfceItem;
use App\Models\PdvDuplicata;
use App\Models\PdvItemVenda;
use App\Models\PdvVenda;
use App\Observers\ClienteObserver;
use App\Observers\ContaReceberObserver;
use App\Observers\ItemLojaPedidoObserver;
use App\Observers\ItemNfceObserver;
use App\Observers\ItemPdvVendaObserver;
use App\Observers\ItemVendaBalcaoObserver;
use App\Observers\LojaPedidoObserver;
use App\Observers\MovimentoObserver;
use App\Observers\PdvDuplicataObserver;
use App\Observers\PdvVendaObserver;
use App\Observers\RecebimentoObserver;
use App\Observers\WebHookObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\GradeMovimento;
use App\Observers\GradeMovimentoObserver;
use App\Models\ItemPedidoDelivery;
use App\Observers\ItemPedidoDeliveryObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
        NfceItem::observe(ItemNfceObserver::class);
        Movimento::observe(MovimentoObserver::class);
        PdvDuplicata::observe(PdvDuplicataObserver::class);
        FinRecebimento::observe(RecebimentoObserver::class);
        FinContaReceber::observe(ContaReceberObserver::class);
        ItemVendaBalcao::observe(ItemVendaBalcaoObserver::class);
        LojaItemPedido::observe(ItemLojaPedidoObserver::class);
        Cliente::observe(ClienteObserver::class);
        LojaPedido::observe(LojaPedidoObserver::class);
        LojaItemPedido::observe(ItemLojaPedidoObserver::class);
        PdvVenda::observe(PdvVendaObserver::class);
        PdvItemVenda::observe(ItemPdvVendaObserver::class);
        MercadoPagoTransacao::observe(WebHookObserver::class);
        GradeMovimento::observe(GradeMovimentoObserver::class);
        ItemPedidoDelivery::observe(ItemPedidoDeliveryObserver::class);
    }
}
