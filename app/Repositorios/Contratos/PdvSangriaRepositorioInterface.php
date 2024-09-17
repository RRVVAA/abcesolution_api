<?php
namespace App\Repositorios\Contratos;

interface PdvSangriaRepositorioInterface
{
    public function salvar($dados, $usuario);
    public function listaPorCaixa($caixa_id);
    public function listaPorUsuario($usuario_uuid);
}

