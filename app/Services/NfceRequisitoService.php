<?php
namespace App\Services;

use App\Models\CertificadoDigital;
use App\Models\Emitente;
use App\Models\NaturezaOperacao;
use App\Models\PdvCaixaNumero;
use App\Models\Produto;
use App\Models\Tributacao;

class NfceRequisitoService{
    public static function pendencia($empresa_id){
        $retorno            = new \stdClass();
        $retorno->tem_erro  = false;
        $retorno->erro      = array();
        $retorno->caminho   = array();
        //Existe natureza de Operação
         $natureza_operacao = NaturezaOperacao::where(["padrao"=>config('constantes.padrao_natureza.PDV'), "empresa_id" => $empresa_id])->first();
         if(!$natureza_operacao){
             $retorno->tem_erro = true;
             $retorno->erro[] = "Cadastre uma Natureza de Operação Padrão para o PDV";
             $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Natureza da Operação</span>';
             $retorno->erro[] = "Cadastre um conjunto de Tributação Padrão para o PDV";
             $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Natureza da Operação<b>&#10132;</b> Inserir Tributação</span>';
         
         }else{
         $tributacao         = Tributacao::where(["natureza_operacao_id" =>$natureza_operacao->id,"padrao"=>"S"])->first();
         if(!$tributacao){
             $retorno->tem_erro = true;
             $retorno->erro[] = "- Cadastre um conjunto de Tributação Padrão para o PDV";
             $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Natureza da Operação<b>&#10132;</b> Inserir Tributação</span>';
           }
         }
         
         $emitente = Emitente::where("empresa_id", $empresa_id)->first();
         if($emitente){
         if(!$emitente->cnpj){
             $retorno->tem_erro = true;
             $retorno->erro[] = "- Cadastre um Emitente para poder emitir NFCE";
             $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Emitente NFE</span>';
             $retorno->erro[] = "- Os campos CSC e CSC_ID precisam ter um valor para poder emitir a NFCE";
             $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Emitente NFE<b>&#10132;</b> Aba NFE/NFCE</span>';
             $retorno->erro[] = "- É preciso instalar o Certificado Digital para poder emitir a NFCE";
             $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b>Certificado Digital</span>';
         }
         
         if(!$emitente->csc || !$emitente->csc_id){
         $retorno->tem_erro = true;
         $retorno->erro[] = "- Os campos CSC e CSC_ID precisam ter um valor para poder emitir a NFCE";
         $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Emitente NFE<b>&#10132;</b> Aba NFE/NFCE</span>';
         }
         
       
         
         $certificado = CertificadoDigital::where("emitente_id", $emitente->id)->first();
         if(!$certificado){
         $retorno->tem_erro = true;
         $retorno->erro[] = "- É preciso instalar o Certificado Digital para poder emitir a NFCE";
         $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b>Certificado Digital</span>';
         }else{
         if(!$certificado->certificado_senha ){
         $retorno->erro[] = "- É preciso instalar o Certificado Digital para poder emitir a NFCE";
         $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b>Certificado Digital</span>';
         }
         }
         
         
         }else{
         $retorno->tem_erro = true;
         $retorno->erro[] = "Cadastre um Emitente para poder emitir NFCE";
         $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Emitente NFE</span>';
         $retorno->erro[] = "Os campos CSC e CSC_ID precisam ter um valor para poder emitir a NFCE";
         $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Emitente NFE<b>&#10132;</b> Aba NFE/NFCE</span>';
         $retorno->erro[] = "É preciso instalar o Certificado Digital para poder emitir a NFCE";
         $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b>Certificado Digital</span>';
         
         }
         
        $numCaixa = PdvCaixaNumero::where("empresa_id", $empresa_id)->first();
        if(!$numCaixa){
            $retorno->tem_erro = true;
            $retorno->erro[] = "É necessário ter pelo um Número de PDV cadastrado. ";
            $retorno->caminho[] = 'Menu <b>&#10132;</b> PDV <b>&#10132;</b>Número PDV</span>';
        }
        
        $produto = Produto::where("empresa_id", $empresa_id)->first();
        if(!$produto){
            $retorno->tem_erro = true;
            $retorno->erro[] = "É necessário ter pelo um Produto cadastrado. ";
            $retorno->caminho[] = 'Menu <b>&#10132;</b> Cadastro <b>&#10132;</b>Produto</span>';
        }
        
        return $retorno;
        
    }
    
    public static function verificar($empresa_id){  
        $retorno            = new \stdClass();
        $retorno->tem_erro  = false;
        $retorno->erro      = array();
        $retorno->caminho   = array();
       //Existe natureza de Operação   
    /*    $natureza_operacao = NaturezaOperacao::where(["padrao"=>config('constantes.padrao_natureza.PDV'), "empresa_id" => $empresa_id])->first();
        if(!$natureza_operacao){
            $retorno->tem_erro = true;
            $retorno->erro[] = "Cadastre uma Natureza de Operação Padrão para o PDV";
            $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Natureza da Operação</span>';
            $retorno->erro[] = "Cadastre um conjunto de Tributação Padrão para o PDV";
            $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Natureza da Operação<b>&#10132;</b> Inserir Tributação</span>';
            
        }else{
            $tributacao         = Tributacao::where(["natureza_operacao_id" =>$natureza_operacao->id,"padrao"=>"S"])->first();
            if(!$tributacao){
                $retorno->tem_erro = true;
                $retorno->erro[] = "- Cadastre um conjunto de Tributação Padrão para o PDV";
                $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Natureza da Operação<b>&#10132;</b> Inserir Tributação</span>';
            }
        }
        
        $emitente = Emitente::where("empresa_id", $empresa_id)->first();
        if($emitente){
            if(!$emitente->cnpj){
                $retorno->tem_erro = true;
                $retorno->erro[] = "- Cadastre um Emitente para poder emitir NFCE";
                $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Emitente NFE</span>';
                $retorno->erro[] = "- Os campos CSC e CSC_ID precisam ter um valor para poder emitir a NFCE";
                $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Emitente NFE<b>&#10132;</b> Aba NFE/NFCE</span>';
                $retorno->erro[] = "- Os campos Indicador de Intermediador, CNPJ e Identificação do Intermediador precisam ter um valor para poder emitir a NFCE";
                $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Emitente NFE<b>&#10132;</b> Aba Outros </span>';
                $retorno->erro[] = "- É preciso instalar o Certificado Digital para poder emitir a NFCE";
                $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b>Certificado Digital</span>';
            }
            
            if(!$emitente->csc || !$emitente->csc_id){
                $retorno->tem_erro = true;
                $retorno->erro[] = "- Os campos CSC e CSC_ID precisam ter um valor para poder emitir a NFCE";
                $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Emitente NFE<b>&#10132;</b> Aba NFE/NFCE</span>';
            }
            
            if(!$emitente->indIntermed || !$emitente->cnpjIntermed || !$emitente->idCadIntTran){
                $retorno->tem_erro = true;
                $retorno->erro[] = "- Os campos Indicador de Intermediador, CNPJ e Identificação do Intermediador precisam ter um valor para poder emitir a NFCE";
                $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Emitente NFE<b>&#10132;</b> Aba Outros </span>';
            }
            
            $certificado = CertificadoDigital::where("emitente_id", $emitente->id)->first();
            if(!$certificado){
                $retorno->tem_erro = true;
                $retorno->erro[] = "- É preciso instalar o Certificado Digital para poder emitir a NFCE";
                $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b>Certificado Digital</span>';
            }else{
                if(!$certificado->certificado_senha ){
                    $retorno->erro[] = "- É preciso instalar o Certificado Digital para poder emitir a NFCE";
                    $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b>Certificado Digital</span>';
                }
            }
            
            
        }else{
            $retorno->tem_erro = true;
            $retorno->erro[] = "Cadastre um Emitente para poder emitir NFCE";
            $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Emitente NFE</span>';
            $retorno->erro[] = "Os campos CSC e CSC_ID precisam ter um valor para poder emitir a NFCE";
            $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Emitente NFE<b>&#10132;</b> Aba NFE/NFCE</span>';
            $retorno->erro[] = "Os campos Indicador de Intermediador, CNPJ e Identificação do Intermediador precisam ter um valor para poder emitir a NFCE";
            $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b> Emitente NFE<b>&#10132;</b> Aba Outros</span>';
            $retorno->erro[] = "É preciso instalar o Certificado Digital para poder emitir a NFCE";
            $retorno->caminho[] = 'Menu <b>&#10132;</b> Configurações <b>&#10132;</b>Certificado Digital</span>';
            
        }
     */   
        $numCaixa = PdvCaixaNumero::where("empresa_id", $empresa_id)->first();
        if(!$numCaixa){
            $retorno->tem_erro = true;
            $retorno->erro[] = "É necessário ter pelo um Número de PDV cadastrado. ";
            $retorno->caminho[] = 'Menu <b>&#10132;</b> PDV <b>&#10132;</b>Número PDV</span>';
        }
        
        $produto = Produto::where("empresa_id", $empresa_id)->first();
        if(!$produto){
            $retorno->tem_erro = true;
            $retorno->erro[] = "É necessário ter pelo um Produto cadastrado. ";
            $retorno->caminho[] = 'Menu <b>&#10132;</b> Cadastro <b>&#10132;</b>Produto</span>';
        }
     
        return $retorno;
        
    }
}


