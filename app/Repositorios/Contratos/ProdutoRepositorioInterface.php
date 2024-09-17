<?php
namespace App\Repositorios\Contratos;

interface ProdutoRepositorioInterface
{
    public function produtosPorEmpresaUuid($uuid_empresa);
    public function getProdutosPorEmpresaId(int $id_empresa);
    public function getProdutoPorUuid($uuid);
    public function pesquisaPorNome($nome, $token);
    public function pesquisaPorCodigo($codigo, $token);
    public function pesquisaPorCodigoBarra($barra, $token);
}

