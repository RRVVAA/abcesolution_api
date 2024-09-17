<?php
namespace App\Repositorios\Contratos;

interface ClienteRepositorioInterface
{
    public function clientesPorEmpresaUuid($uuid_empresa);
    public function getClientesPorEmpresaId(int $id_empresa);
    public function getClientePorUuid($uuid);
    public function logar($email, $senha);
}

