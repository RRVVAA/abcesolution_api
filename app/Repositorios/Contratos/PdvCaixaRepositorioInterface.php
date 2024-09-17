<?php
namespace App\Repositorios\Contratos;

interface PdvCaixaRepositorioInterface
{
    public function listaCaixaAbertoPorUsuario($id);
    public function verificaSeTemCaixaAbertoPorUsuario($id);
    public function abrir(array $dados);
    public function atualizar($caixa_id);
    public function getCaixa($caixa_id);
}

