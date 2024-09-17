<?php


function i($array){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
    exit;
}

function verificaValor($val, $excep = false){
    $val = preg_replace('/[^\d\.\,]+/', '', $val);
    // Inteiro
    if (preg_match('/^\d+$/', $val)) {
        $valor = (float) $val;
    } else
        // Float
        if (preg_match('/^\d+\.{1}\d+$/', $val)) {
            $valor = (float) $val;
        } else{
            // Vírgula como separador decimal
            if (preg_match('/^[\d\.]+\,{1}\d+$/', $val)) {
                $valor = (float) str_replace(',','.', str_replace('.', '', $val));
            } else {        // Formato inválido ou em branco
                if($excep)
                    // throw new \Exception('Moeda em formato inválido ou desconhecido.');
                    $valor = 0;
            }
        }
        return $valor;
}

function moedaBr($valor, $simbolo = null, $casasDecimais = 2){
    $valor = verificaValor($valor);
    return ((isset($simbolo) && $simbolo) ? 'R$ ':'').number_format($valor, $casasDecimais, ',', '.');
}

function getFloat($valor, $simbolo = null, $casasDecimais = 2){
    $valor = verificaValor($valor);
    if (isset($casasDecimais))
        return (float) number_format($valor, $casasDecimais,'.','');
        else
            return (float) $valor;
            
}

function moedaEn($valor, $simbolo = null, $casasDecimais = 2){
    $valor = verificaValor($valor);
    return ((isset($simbolo) && $simbolo) ? 'US$ ':'').number_format($valor, $casasDecimais, '.', ',');
}

function zeroEsquerda($str, $qtde){
    return str_pad($str, $qtde,'0',STR_PAD_LEFT);
}
function getCodUF($uf){
    $estados = [
        '11' => 'RO',
        '12' => 'AC',
        '13' => 'AM',
        '14' => 'RR',
        '15' => 'PA',
        '16' => 'AP',
        '17' => 'TO',
        '21' => 'MA',
        '22' => 'PI',
        '23' => 'CE',
        '24' => 'RN',
        '25' => 'PB',
        '26' => 'PE',
        '27' => 'AL',
        '28' => 'SE',
        '29' => 'BA',
        '31' => 'MG',
        '32' => 'ES',
        '33' => 'RJ',
        '35' => 'SP',
        '41' => 'PR',
        '42' => 'SC',
        '43' => 'RS',
        '50' => 'MS',
        '51' => 'MT',
        '52' => 'GO',
        '53' => 'DF'
    ];
    foreach($estados as $key => $e){
        if($uf == $e) return $key;
    }
}

function retornoCartao($valor){
    $dados= [
        'accredited' => 'Pronto, seu pagamento foi aprovado',
        'pending_contingency' => 'Estamos processando seu pagamento. Não se preocupe, em menos de 2 dias úteis informaremos por e-mail se foi creditado. ',
        'pending_review_manual' => 'Estamos processando seu pagamento. Não se preocupe, em menos de 2 dias úteis informaremos por e-mail se foi creditado ou se necessitamos de mais informação',
        'cc_rejected_bad_filled_card_number' => 'Revise o número do cartão.',
        'cc_rejected_bad_filled_date' => 'Revise a data de vencimento.',
        'cc_rejected_bad_filled_other' => 'Revise os dados.',
        'cc_rejected_bad_filled_security_code' => 'Revise o código de segurança do cartão.',
        'cc_rejected_blacklist' => 'Não pudemos processar seu pagamento.',
        'cc_rejected_call_for_authorize' => 'Você deve autorizar ao payment_method_id o pagamento do valor ao Mercado Pago.',
        'cc_rejected_card_disabled' => 'Ligue para o payment_method_id para ativar seu cartão. O telefone está no verso do seu cartão.',
        'cc_rejected_card_error' => 'Não conseguimos processar seu pagamento.',
        'cc_rejected_duplicated_payment' => 'Você já efetuou um pagamento com esse valor. Caso precise pagar novamente, utilize outro cartão ou outra forma de pagamento.',
        'cc_rejected_high_risk' => 'Escolha outra forma de pagamento. Recomendamos meios de pagamento em dinheiro.',
        'cc_rejected_insufficient_amount' => 'O payment_method_id possui saldo insuficiente.',
        'cc_rejected_invalid_installments' => 'O payment_method_id não processa pagamentos em installments parcelas.',
        'cc_rejected_max_attempts' => 'Escolha outro cartão ou outra forma de pagamento.',
        'cc_rejected_other_reason' => 'payment_method_id não processa o pagamento.',
        'cc_rejected_card_type_not_allowed' => 'O pagamento foi rejeitado porque o usuário não tem a função crédito habilitada em seu cartão multiplo (débito e crédito).',        
    ];
    
    return $dados[$valor];
}

function retornoCartaoToken($valor){
    $dados= [
        '106' => 'Não pode efetuar pagamentos a usuários de outros países.',
        '109' => 'O payment_method_id não processa pagamentos parcelados. ',
        '126' => 'Não conseguimos processar seu pagamento.',
        '129' => 'O payment_method_id não processa pagamentos para o valor selecionado. Escolha outro cartão ou outra forma de pagamento.',
        '145' => 'Uma das partes com a qual está tentando realizar o pagamento é um usuário de teste e a outra é um usuário real.',
        '150' => 'Você não pode efetuar pagamentos.',
        '151' => 'Você não pode efetuar pagamentos.',
        '160' => 'Não conseguimos processar seu pagamento. O payment_method_id não está disponível nesse momento.',
        '204' => 'O payment_method_id não está disponível nesse momento. Escolha outro cartão ou outra forma de pagamento.',
        '801' => 'Você realizou um pagamento similar há poucos instantes. Tente novamente em alguns minutos.',
     ];
    
    return $dados[$valor];
}

function dataHoraBr($dataHora){
    $r = new Carbon\Carbon($dataHora);
    return  $r->format('d/m/Y H:i');
}

function formataNumeroBr($numero, $dec = 2)
{
    return number_format( $numero, $dec, ',', '.' );
}

function formataNumero($number, $dec = 2)
{
    return number_format((float) $number, $dec, ".", "");
}

function tira_mascara($valor){
    return  preg_replace("/\D+/", "", $valor);
}

function objToArray($objeto){
    return is_array($objeto) ? $objeto : (array) $objeto;
}
function hoje(){
    return date("Y-m-d");
}

function agora(){
    return date("H:i:s");
}

function databr($value, $format = 'd/m/Y'){
    return Carbon\Carbon::parse($value)->format($format);
}

function tiraAcento($str){
    $comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
    $semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', '0', 'U', 'U', 'U');
    return str_replace($comAcentos, $semAcentos, $str);
}


function extrair_data($data, $opcao=1){
    //Opção 1-EN 2-BR
    if ($opcao==1){
        $dia = substr($data,8,2);
        $mes = substr($data,5,2);
        $ano = substr($data,0,4);
    }
    else{
        $dia = substr($data,0,2);
        $mes = substr($data,3,2);
        $ano = substr($data,6,4);
    }
    return array($dia,$mes,$ano);
}

function somarData($data, $dias=0, $meses=0, $ano=0, $opcao=1 ){
    $data = extrair_data($data, $opcao);
    $resData2 = date("Y-m-d", mktime(0, 0, 0,$data[1] + $meses,   $data[0] + $dias, $data[2] + $ano));
    return $resData2;
}

function getIDDiaSemana($yyyymmdd){
    $dia = new Carbon\Carbon($yyyymmdd);
    return $dia->dayOfWeek + 1;
}

function getNomeDiaSemana($diaSemana){
    switch ($diaSemana){
        case 0:
            return 'Dom';
        case 1:
            return 'Seg';
        case 2:
            return 'Ter';
        case 3:
            return 'Qua';
        case 4:
            return 'Qui';
        case 5:
            return 'Sex';
        case 6:
            return 'Sab';
        default:
            return null;
            
    }
}

//função limata caracteres
function limita_caracteres($texto, $limite, $quebra = true){
    $tamanho = strlen($texto);
    if($tamanho <= $limite){ //Verifica se o tamanho do texto é menor ou igual ao limite
        $novo_texto = $texto;
    }else{ // Se o tamanho do texto for maior que o limite
        if($quebra == true){ // Verifica a opção de quebrar o texto
            $novo_texto = trim(substr($texto, 0, $limite))."...";
        }else{ // Se não, corta $texto na última palavra antes do limite
            $ultimo_espaco = strrpos(substr($texto, 0, $limite), " "); // Localiza o útlimo espaço antes de $limite
            $novo_texto = trim(substr($texto, 0, $ultimo_espaco))."..."; // Corta o $texto até a posição localizada
        }
    }
    return $novo_texto; // Retorna o valor formatado
}

function validaArrayHorarios( $vetHorarios, $data ){
    
    $hoje = new Carbon\Carbon( date("Y-m-d") );
    $data_ref = new Carbon\Carbon( $data );
    
    if ( $hoje->eq( $data_ref ) ) {
        
        foreach ( $vetHorarios->horario->horas as $hora  ) {
            
            $hora_banco = Carbon\Carbon::createFromTimeString( date("Y-m-d ".$hora->hora.":00") );
            
            $diferenca = $hora_banco->diffInMinutes( now() );
            //numero de minutos necessários para compra no mesmo dia
            $intervalo_minimo = 120;
            
            if (  now()->gt( $hora_banco ) or ( now()->lt( $hora_banco ) and $diferenca < $intervalo_minimo ) ) {
                $hora->setIndisponivel();
            }
        }
        
    }
    return $vetHorarios;
}
