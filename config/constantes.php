<?php
return [
    'status' => [
        'ATIVO'             => 1, // Utilizada para indicar que um recurso/modelo está ATIVO
        'DIGITACAO'         => 2, // Utilizada para indicar que um recurso/modelo Está em Digitacao
        'CONCRETIZADO'      => 3, // Utilizada para indicar que um recurso/modelo é novo
        'CANCELADO'         => 4, // Utilizada para indicar que um recurso/modelo é novo
        'DELETADO'          => 5, // Indica que algo foi cancelado automaticamente ou via administrativa
        'PENDENTE'          => 6, // Utilizada para indicar que um recurso/modelo foi DELETADO e não deve ser utilizado
        'ABERTO'            => 7,// Indica que algo está pendente
        'PARCIALMENTE_PAGO' => 8,// Utilizada quando ser quer setar um elemento como obrigatório para o sistema (indeletável)
        'PAGO'              => 9,
        'ATRASADO'          => 10,// Utilizada quando ser quer setar um elemento como obrigatório para o sistema (indeletável)
        'VENCIDO'           => 11,
        'ERRO_AO_GERAR_XML' => 12,
        'ERRO_AO_ASSINAR_XML' => 13,
        'ERRO_AO_ENVIAR_NFE'=> 14,
        'DENEGADO'          => 15,
        'REJEITADO'         => 16,
        'AUTORIZADO'        => 17, // Utilizada para indicar que um recurso/modelo está INATIVO
        'FINALIZADO'        => 18, // Indica que algo encerrou seu fluxo natural
        'ENVIADO'           => 19, // Quando couber o status ENVIADO para um recurso
        'INDISPONIVEL'      => 20,// Quando couber o status reservado para uma acomodação
        'OCUPADO'           => 21,// Quando couber o status reservado para uma acomodação
        'DISPONIVEL'        => 22,// Quando a acomodação está disponível
        'OBRIGATORIO'       => 23,// Utilizada quando ser quer setar um elemento como obrigatório para o sistema (indeletável)
        'FECHADO'           => 24,// Utilizada quando ser quer setar um elemento como obrigatório para o sistema (indeletável)
        'RECUSADO'          => 25,
        'EM_TRANSITO'       => 26,
        'ENTREGUE'          => 27,
        'EM_DIAS'           => 28,
        'BLOQUEADO'         => 29,
        'LIBERADO'          => 30,
        'DEMO'              => 31,
        'DEMO_VENCIDO'      => 32,
        'NAO_ASSINANTE'     => 33,
        'PROSPECTO'         => 34,
        'NOVO'              => 35,
        'INUTILIZADO'       => 36,
        'EM_PROCESSAMENTO'  => 37,
        'AGUARDANDO_PAGAMENTO'  => 38,
        'PDVVENDA_SALVA'    => 39,
        'PDVVENDA_INICIADA' => 40,
    ],
   
    'perfil' => [
        'ADMIN_MASTER' => 1, // Acesso irrestrito
        'ADMIN_EMPRESA' => 2, //Acesso irrestristo no escopo da Empresa
        'OPERADOR_EMBARQUE' => 3, // Acesso a área de embarques
        'OPERADOR_VENDAS' => 4, //Acesso área de vendas
    ],
    
    'tipo_recorrencia' => [
        'MENSAL'       => 1, 
        'TRIMESTRAL'   => 3,
        'SEMESTRAL'    => 6,
        'ANUAL'        => 12,
    ],
    'tipo_documento' => [
        'VENDA'      => 1,
        'COMPRA'     => 2,
        'DESPESA'    => 3,
        'FATURA'     => 4,
        'AVULSO'     => 5,
        'CONTA_PAGAR'     => 6,
        'CONTA_RECEBER'     => 7,
    ],
    
    'dia_semana' => [
        'DOMINGO'   => 1,
        'SEGUNDA'   => 2,
        'TERCA'     => 3,
        'QUARTA'    => 4,
        'QUINTA'    => 5,
        'SEXTA'     => 6,
        'SABADO'    => 7,
    ],
    
    'forma_pagto'   => [
        'DINHEIRO'              => 1,
        'CHEQUE'                => 2,
        'CARTAO_CREDITO'        => 3,
        'CARTAO_DEBITO'         => 4,
        'CREDITO_LOJA'          => 5,
        'VALE_ALIMENTACAO'      => 10,
        'VALE_REFEICAO'         => 11,
        'VALE_PRESENTE'         => 12,
        'VALE_COMBUSTIVEL'      => 13,
        'DUPLICATA_MERCANTIL'   => 14,
        'BOLETO_BANCARIO'       => 15,
        'DEPOSITO_BANCARIO'     => 16,
        'PIX'                   => 17,
        'PROGRAMA_FIDELIDADE'   => 18,
        'SEMP_PAGAMENTO'        => 90,
        'OUTROS'                => 99,
        'CHECKOUT_PIX'          => 100,
    ],
    
    
    'pagamento'   => [
        '01' => 'Dinheiro',
        '02' => 'Cheque',
        '03' => 'Cartão de Crédito',
        '04' => 'Cartão de Débito',
        '05' => 'Crédito Loja',
        '10' => 'Vale Alimentação',
        '11' => 'Vale Refeição',
        '12' => 'Vale Presente',
        '13' => 'Vale Combustível',
        '14' => 'Duplicata Mercantil',
        '15' => 'Boleto Bancário',
        '16' => 'Depósito Bancário',
        '17' => 'PIX',
        '18' => 'Programa Fidelidade',
        '90' => 'Sem pagamento',
        '99' => 'Outros',
        '100' => 'Checkout com PIX',
    ],
    
    'status_entrega' => [
        'PEDIDO_INICIADO' => 1,
        'PAGAMENTO_CONFIRMADO' => 2,
        'SEPARACAO_PRODUTO' => 3,
        'ENVIADO_PARA_ENTREGA' => 4,
        'EM_TRANSITO' => 5,
        'ENTREGUE' => 6,
    ],

    
    'situacao_financeira' => [
        'INICIADO'   => 1,
        'REPROVADO'  => 2,
        'APROVADO'   => 3,
        'ESTORNADA_PARCIALMENTE' => 4,
        'RESSARCIDA' => 5,
        'RESSARCIDA_PARCIALMENTE' => 6,
        'CHAGEBACK' => 7
    ],
    
    'tipo_operacao' => [
        'CREDITO' => 1,
        'DEBITO' => 2
    ],
    
    'padrao_natureza' => [
        'VENDA'             => '1',
        'COMPRA'            => '2',
        'PDV'               => '3',
        'LOJA'              => '4',
        'DEVOLUCAO_VENDA'   => '5',
        'DEVOLUCAO_COMPRA'  => '6',
    ],
    
    'tipo_notafiscal' => [
        'VENDA'             => '1',
        'COMPRA'           => '2',
        'DEVOLUCAO_VENDA'   => '3',
        'DEVOLUCAO_COMPRA'  => '4',
        'COMPLEMENTAR'  => '5',
        'CANCELAMENTO_FORA_PRAZO'  => '6',
        'AJUSTE'  => '7',
        'REMESSA_CONSERTO'  => '8',
        'RETORNO_CONSERTO'  => '9',
        'REMESSA_DEMONSTRACAO'  => '10',
        'RETORNO_DEMONSTRACAO'  => '11',
        'REMESSA_ARMAZENAGEM'  => '12',
        'RETORNO_ARMAZENAGEM'  => '13',
        'TRANSFERENCIA'  => '14',
        'BONIFICACAO'  => '15',
        'EXPORTACAO'  => '16'
    ],
  
    'tipo_movimento' => [
        'ENTRADA_INICIO_ESTOQUE'        => '1',
        'ENTRADA_AVULSA'                => '2',
        'ENTRADA_AJUSTE_BALANCO'        => '3',
        'ENTRADA_COMPRA_MANUAL'         => '4',
        'ENTRADA_IMPORTACAO_XML'        => '5',
        'ENTRADA_TRANSFERENCIA_ESTOQUE' => '6',
        'ENTRADA_CANCELAMENTO_VENDA'    => '7',
        'ENTRADA_ESTORNO_MANUAL'        => '8',
        'ENTRADA_CANCELAMENTO_NFE'      => '9',
        'ENTRADA_ESTORNO_MANUAL_PDV'    => '10',
        'ENTRADA_CANCELAMENTO_PDV'      => '11',
        'SAIDA_AVULSA'                  => '12',
        'SAIDA_PERDA'                   => '13',
        'SAIDA_VENDA_PRODUTO'           => '14',
        'SAIDA_ORDEM_PRODUCAO'          => '15',
        'SAIDA_AJUSTE_BALANCO'          => '16',
        'SAIDA_CONSUMO_INTERNO'         => '17',
        'SAIDA_TRANSFERENCIA_ESTOQUE'   => '18',
        'SAIDA_VENDA_LOJA_VIRTUAL'      => '19',
        'SAIDA_ESTORNO_MANUAL'          => '20',
        'SAIDA_CANCELAMENTO_COMPRA'     => '21',
        'SAIDA_EMISSAO_NFE'             => '22',
        'SAIDA_VENDA_PDV'               => '23',
        'SEM_MOVIMENTO'                 => '24'
    ],
    
    'desc_tipo_movimento' => [
        'Entrada Início de Estoque'         => '1',
        'Entrada Avulsa'                    => '2',
        'Entrada Por Ajuste de Balanço'     => '3',
        'Entrada por Compra Manual'         => '4',
        'Entrada por Importação XML'        => '5',
        'Entrada Transferencia de Estoque'  => '6',
        'Entrada Cancelamento de Venda'     => '7',
        'Entrada por Estorno Manual'        => '8',
        'Entrada por Cancelamento de NFE'   => '9',
        'Entrada por Estorno Manual PDV'    => '10',
        'Entrada por Cancelamento PDV'      => '11',        
        'Saída Avulsa'                      => '12',
        'Saída por Perda'                   => '13',
        'Saída por Venda de Produto'        => '14',
        'Saída por Ordem de Produção'       => '15',
        'Saída por Ajuste de Estoque'       => '16',
        'Saida para Consumo Interno'        => '17',
        'Saída por Transferência de Estoque'=> '18',
        'Saída Venda na Loja Virtual'       => '19',
        'Saída por Estorno Manual'          => '20',
        'Saída Cancelamento de Compra'      => '21',
        'Saída por Emissão de NFE'          => '22',
        'Saída por Venda por PDV'           => '22'
    ],

];
