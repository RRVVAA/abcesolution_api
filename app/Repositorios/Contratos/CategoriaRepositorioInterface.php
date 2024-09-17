<?php
namespace App\Repositorios\Contratos;

interface CategoriaRepositorioInterface
{
    public function categoriasPorEmpresaUuid($uuid_empresa);
    public function getCategoriasPorEmpresaId($id_empresa);
    public function getCategoriaPorUuid($uuid);
}

