<?php
namespace App\Repositorios\Contratos;

interface PedidoClienteRepositorioInterface
{
    public function criarNovoPedido(
        string $identificador,
        float $total,
        string $status_id,
        int $empresa_id,
        $cliente_id,
        string $origem,
        $observacao
    );
    public function getPedidoPorIdentificador(string $identificador);
    public function inserirItensPedido(int $id_pedido, array $itens);
    public function filtro($data1, $data2, $cliente_id);
}

