<?php
namespace App\Services;

use App\Models\MovimentoConta;
use App\Models\Emitente;

class MovimentoContaBancariaService{
    public static function inserirMovimentoSangrira($sangria ){
        $emitente                           = Emitente::where("empresa_id", $sangria->empresa_id)->first();
        $mov                                = new \stdClass();
        $mov->empresa_id                    = $sangria->empresa_id;
        $mov->sangria_id                    = $sangria->id;
        $mov->conta_id                      = $emitente->pdv_conta_corrente_id ;
        $mov->documento                     = "Sangria Caixa#".$sangria->id;
        $mov->data_emissao                  = hoje();
        $mov->data_compensacao              = hoje();
        $mov->tipo_movimento                = "C";
        $mov->historico                     = "Entrada de Valor via Sangria #".$sangria->id;
        $mov->valor                         = $sangria->valor;
        $mov->origem                        = "sangria";
        $mov->classificacao_financeira_id   = $emitente->pdv_classificacao_financeira_id;
        
        MovimentoConta::Create(objToArray($mov));
    }
    
    
    public static function inserirMovimentoSuplemento($suplemento){
        $emitente                           = Emitente::where("empresa_id", $suplemento->empresa_id)->first();
        $mov                                = new \stdClass();
        $mov->empresa_id                    = $suplemento->empresa_id;
        $mov->suplemento_id                 = $suplemento->id;
        $mov->conta_id                      = $emitente->pdv_conta_corrente_id ;
        $mov->documento                     = "Suplemento Caixa#".$suplemento->id;
        $mov->data_emissao                  = hoje();
        $mov->data_compensacao              = hoje();
        $mov->tipo_movimento                = "D";
        $mov->historico                     = "Saída de Valor via Suplemento #".$suplemento->id;
        $mov->valor                         = $suplemento->valor;
        $mov->classificacao_financeira_id   = $emitente->pdv_classificacao_financeira_id;
        
        MovimentoConta::Create(objToArray($mov));
    }
    
    public static function inserirMovimentoRecebimento($recebimento, $origem, $historico){
        $origem =$origem ?? null;
        if(isset($recebimento->conta_receber)){
            if($recebimento->conta_receber->venda_id){
                $origem = "Venda ERP";
            }
            
            if($recebimento->conta_receber->pdvduplicata_id ){
                $origem = "Venda PDV";
            }
            
            if($recebimento->conta_receber->loja_pedido_id ){
                $origem = "Venda Loja Virtual";
            }
            
            if($recebimento->conta_receber->cobranca_id ){
                $origem = "Recebimento de Cobrança";
            }
        }
        
        $mov                                = new \stdClass();
        $mov->empresa_id                    = $recebimento->empresa_id;
        $mov->recebimento_id                = $recebimento->id;
        $mov->usuario_id                    = $recebimento->usuario_id;
        $mov->conta_id                      = $recebimento->conta_corrente_id ;
        $mov->documento                     = "Recebimento #".$recebimento->id;
        $mov->data_emissao                  = hoje();
        $mov->data_compensacao              = hoje();
        $mov->tipo_movimento                = "C";
        $mov->historico                     = $historico;
        $mov->valor                         = $recebimento->valor_recebido;
        $mov->origem                        = $origem;
        $mov->classificacao_financeira_id   = $recebimento->classificacao_financeira_id;
        MovimentoConta::Create(objToArray($mov));
    }
    
    public static function inserirMovimentoPagamento($recebimento ){
        
        
        $mov                                = new \stdClass();
        $mov->empresa_id                    = $recebimento->empresa_id;
        $mov->recebimento_id                = $recebimento->id;
        $mov->conta_id                      = $recebimento->conta_corrente_id ;
        $mov->documento                     = "Recebimento #".$recebimento->id;
        $mov->data_emissao                  = hoje();
        $mov->data_compensacao              = hoje();
        $mov->tipo_movimento                = "C";
        $mov->historico                     = "Entrada de Valor via Recebimento #".$recebimento->id;
        $mov->valor                         = $recebimento->valor_recebido;
        $mov->classificacao_financeira_id   = $recebimento->classificacao_financeira_id;
       
        
        MovimentoConta::Create(objToArray($mov));
    }

}

