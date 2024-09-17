<?php

namespace App\Providers;

use App\Repositorios\CategoriaRepositorio;
use App\Repositorios\ClienteRepositorio;
use App\Repositorios\EmpresaRepositorio;
use App\Repositorios\LojaBannerRepositorio;
use App\Repositorios\LojaConfiguracaoRepositorio;
use App\Repositorios\LojaPedidoRepositorio;
use App\Repositorios\PdvCaixaRepositorio;
use App\Repositorios\PdvNumeroRepositorio;
use App\Repositorios\PdvSangriaRepositorio;
use App\Repositorios\PdvSuplementoRepositorio;
use App\Repositorios\PdvVendaRepositorio;
use App\Repositorios\PedidoClienteRepositorio;
use App\Repositorios\ProdutoRepositorio;
use App\Repositorios\UsuarioRepositorio;
use App\Repositorios\Contratos\CategoriaRepositorioInterface;
use App\Repositorios\Contratos\ClienteRepositorioInterface;
use App\Repositorios\Contratos\EmpresaRepositorioInterface;
use App\Repositorios\Contratos\LojaBannerRepositorioInterface;
use App\Repositorios\Contratos\LojaConfiguracaoRepositorioInterface;
use App\Repositorios\Contratos\LojaPedidoRepositorioInterface;
use App\Repositorios\Contratos\PdvCaixaRepositorioInterface;
use App\Repositorios\Contratos\PdvNumeroRepositorioInterface;
use App\Repositorios\Contratos\PdvSangriaRepositorioInterface;
use App\Repositorios\Contratos\PdvSuplementoRepositorioInterface;
use App\Repositorios\Contratos\PdvVendaRepositorioInterface;
use App\Repositorios\Contratos\PedidoClienteRepositorioInterface;
use App\Repositorios\Contratos\ProdutoRepositorioInterface;
use App\Repositorios\Contratos\UsuarioRepositorioInterface;
use Illuminate\Support\ServiceProvider;

class RepositorioServiceProvider extends ServiceProvider
{
    
    public function register()
    {
        $this->app->bind(EmpresaRepositorioInterface::class, EmpresaRepositorio::class );
        $this->app->bind(CategoriaRepositorioInterface::class, CategoriaRepositorio::class );
        $this->app->bind(ProdutoRepositorioInterface::class, ProdutoRepositorio::class );
        $this->app->bind(PedidoClienteRepositorioInterface::class, PedidoClienteRepositorio::class );
        $this->app->bind(ClienteRepositorioInterface::class, ClienteRepositorio::class );
        $this->app->bind(LojaConfiguracaoRepositorioInterface::class, LojaConfiguracaoRepositorio::class);
        $this->app->bind(LojaBannerRepositorioInterface::class, LojaBannerRepositorio::class);
        $this->app->bind(LojaPedidoRepositorioInterface::class, LojaPedidoRepositorio::class);
        $this->app->bind(PdvCaixaRepositorioInterface::class, PdvCaixaRepositorio::class);
        $this->app->bind(UsuarioRepositorioInterface::class, UsuarioRepositorio::class);
        $this->app->bind(PdvNumeroRepositorioInterface::class, PdvNumeroRepositorio::class);
        $this->app->bind(PdvVendaRepositorioInterface::class, PdvVendaRepositorio::class);
        $this->app->bind(PdvSangriaRepositorioInterface::class, PdvSangriaRepositorio::class);
        $this->app->bind(PdvSuplementoRepositorioInterface::class, PdvSuplementoRepositorio::class);
    }
    
    public function boot()
    {
        //
    }
}
