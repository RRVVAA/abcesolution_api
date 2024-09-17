<?php
namespace App\Repositorios\Contratos;

interface PdvVendaRepositorioInterface
{    
    public function getVendaAbertaPorUsuario($id, $caixa_id);
    public function getVendaPorId($id_venda);
    public function novaVenda($usuario_id, $caixa_id, $empresa_id);
    public function salvar($dados);
    public function inserirItensVenda(int $id_venda, $itens);
    public function inserirPagamentos(int $id_venda, $id_caixa, $pagamentos);
    public function listaPorCaixa($caixa_id);
    public function listaPorUsuario($usuario_uuid);
}

