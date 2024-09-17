<?php
namespace App\Repositorios\Contratos;

interface EmpresaRepositorioInterface
{
    public function getAllEmpresas();
    public function getEmpresaPorUuid($uuid);
}

