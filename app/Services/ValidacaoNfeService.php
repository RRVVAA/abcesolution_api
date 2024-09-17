<?php
namespace App\Services;

use function PHPUnit\Framework\isEmpty;

class ValidacaoNfeService{
    public static function dadosNFe($dados){  
        $dados = (object) $dados; 
       //i($dados);
        //Verifica se existe
        if(!isset($dados->cUF)){
            return "Campo " . "cUF " . "Não Existe!" ;            
        }
        
        if(!isset($dados->natOp)){
            return "Campo " . "natOp " . "Não Existe!" ;
        }
        
        if(!isset($dados->mod)){
            return "Campo " . "mod " . "Não Existe!" ;
        }
        
        if(!isset($dados->serie)){
            return "Campo " . "serie " . "Não Existe!" ;
        }
                
        if(!isset($dados->tpNF)){
            return "Campo " . "tpNF " . "Não Existe!" ;
        }
        
        if(!isset($dados->idDest)){
            return "Campo " . "idDest " . "Não Existe!" ;
        }
        
        if(!isset($dados->cMunFG)){
            return "Campo " . "cMunFG " . "Não Existe!" ;
        }
        
        if(!isset($dados->tpImp)){
            return "Campo " . "tpImp " . "Não Existe!" ;
        }
        if(!isset($dados->tpEmis)){
            return "Campo " . "tpEmis " . "Não Existe!" ;
        }
        if(!isset($dados->tpAmb)){
            return "Campo " . "tpAmb " . "Não Existe!" ;
        }
        if(!isset($dados->finNFe)){
            return "Campo " . "finNFe " . "Não Existe!" ;
        }
        if(!isset($dados->indFinal)){
            return "Campo " . "indFinal " . "Não Existe!" ;
        }
        if(!isset($dados->indPres)){
            return "Campo " . "indPres " . "Não Existe!" ;
        }
        if(!isset($dados->procEmi)){
            return "Campo " . "procEmi " . "Não Existe!" ;
        }
        if(!isset($dados->verProc)){
            return "Campo " . "verProc " . "Não Existe!" ;
        }
        if(!isset($dados->em_xNome)){
            return "Campo " . "xNome " . "Não Existe!" ;
        }
        if(!isset($dados->em_xLgr)){
            return "Campo " . "xLgr " . "Não Existe!" ;
        }
        if(!isset($dados->em_nro)){
            return "Campo " . "nro " . "Não Existe!" ;
        }
        if(!isset($dados->em_xBairro)){
            return "Campo " . "xBairro " . "Não Existe!" ;
        }
        if(!isset($dados->em_cMun)){
            return "Campo " . "cMun " . "Não Existe!" ;
        }
        if(!isset($dados->em_xMun)){
            return "Campo " . "xMun " . "Não Existe!" ;
        }
        if(!isset($dados->em_UF)){
            return "Campo " . "UF " . "Não Existe!" ;
        }
        if(!isset($dados->em_CEP)){
            return "Campo " . "CEP " . "Não Existe!" ;
        }
        if(!isset($dados->em_IE)){
            return "Campo " . "IE " . "Não Existe!" ;
        }
        
        //Verifica conteúdo
        
        
        if($dados->cUF==""){
            return   "cUF - Código da UF do emitente do Documento Fiscal Obrigatório";        
        } 
        if($dados->natOp==""){
            return   "NatOp - Descrição da Natureza da Operação Obrigatório";
        }
        if($dados->mod==""){
            return   "mod - Código do Modelo do Documento Fiscal";
        }
        if($dados->serie==""){
            return   " Série do Documento Fiscal";
        }
       
        if( ($dados->tpNF=="" || $dados->tpNF== null ) ||  (intval($dados->tpNF) != 0 && $dados->tpNF != 1)){
            return   "Tipo de Operação";
        }
        if(($dados->idDest=="" || $dados->idDest== null ) || ($dados->idDest!=1  && $dados->idDest!= 2  && $dados->idDest!= 3 ) ){
            return   "idDest - Identificador de local de destino da operação";
        }
        if($dados->cMunFG==""){
            return   " Código do Município de Ocorrência do Fato Gerador";
        }
        if(($dados->tpImp=="" || $dados->tpImp== null ) || (intval($dados->tpImp)!=0 && $dados->tpImp!=1 && $dados->tpImp!= 2
                                                             && $dados->tpImp!= 3  && $dados->tpImp!= 4 && $dados->tpImp!= 5 )){
            return   "tpImp - Formato de Impressão do DANFE";
        }
        if(($dados->tpEmis=="" || $dados->tpEmis== null ) || ($dados->tpEmis!=1 && $dados->tpEmis!= 2  && 
            $dados->tpEmis!= 3 && $dados->tpEmis!= 4 && $dados->tpEmis!= 5  && $dados->tpEmis!= 6 && $dados->tpEmis!= 7 && $dados->tpEmis!= 9  )){
            return   "tpEmis - Tipo de Emissão da NF-e";
        }
        if(($dados->tpAmb=="" || $dados->tpAmb== null ) || ($dados->tpAmb !=1 && $dados->tpAmb!=2)){
            return   "tpAmb - Identificação do Ambiente";
        }
        if(($dados->finNFe=="" || $dados->finNFe== null ) || ($dados->finNFe!=1 && $dados->finNFe!= 2  && $dados->finNFe!= 3 && $dados->finNFe!= 4 )){
            return   "finNFe - Finalidade de emissão da NF-e";
        }
        if(($dados->indFinal=="" || $dados->indFinal== null ) ||  (intval($dados->indFinal) != 0 && $dados->indFinal != 1)){
            return   "indFinal - Indica operação com Consumidor final";
        }
        if(($dados->indPres=="" || $dados->indPres== null ) || (intval($dados->indPres)!=0 && $dados->indPres!=1
            && $dados->indPres!= 2  && $dados->indPres!= 5 && $dados->indPres!= 9 )){
            return   "indPres -  Indicador de presença do comprador no estabelecimento comercial no momento da operação";
        }
        if(($dados->procEmi=="" || $dados->procEmi== null ) ||  (intval($dados->procEmi) != 0 && $dados->procEmi != 1 
            && $dados->procEmi != 2 && $dados->procEmi != 3)){
            return   "procEmi - Processo de emissão da NF-e";
        }
        if($dados->verProc==""){
            return   "verProc - Versão do Processo de emissão da NF-e";
        }
        if($dados->em_xNome==""){
            return   "xNome - Razão Social ou Nome do emitente";
        }
        if($dados->em_xLgr==""){
            return   "xLgr -  Logradouro do Endereço do emitent";
        }
        if($dados->em_nro==""){
            return   "nro - Número do Endereço do emitente";
        }
        if($dados->em_xBairro==""){
            return   "xBairro -  Bairro do Endereço do emitente";
        }
        if($dados->em_cMun==""){
            return   "cMun - Código do município do Endereço do emitente";
        }
        if($dados->em_xMun==""){
            return   "xMun - Nome do município do Endereço do emitente";
        }
        if($dados->em_UF==""){
            return   "UF - Sigla da UF do Endereço do emitente";
        }
        if($dados->em_CEP==""){
            return   "CEP - Código do CEP do Endereço do emitente";
        }
        if($dados->em_IE==""){
            return   "IE - Inscrição Estadual do emitente";
        }
        if($dados->em_CRT==""){
            return   "CRT - Código de Regime Tributário do emitente";
        }
    }
    
    public static function dadosItemNfce($itens){   
        foreach($itens as $item){
            $item = (object) $item;
            
            if(!isset($item->cProd)){
                return "Campo " . "cProd " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->cEAN)){
                return "Campo " . "cEAN " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->xProd)){
                return "Campo " . "xProd " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->NCM)){
                return "Campo " . "NCM " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->CFOP)){
                return "Campo " . "CFOP " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->uCom)){
                return "Campo " . "uCom " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->qCom)){
                return "Campo " . "qCom " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->vUnCom)){
                return "Campo " . "vUnCom " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->cEANTrib)){
                return "Campo " . "cEANTrib " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->uTrib)){
                return "Campo " . "uTrib " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->qTrib)){
                return "Campo " . "qTrib " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->vUnTrib)){
                return "Campo " . "vUnTrib " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->indTot)){
                return "Campo " . "indTot " . "Não Existe!" ;
                break;
            }
            if(!isset($item->cstPIS)){
                return "Campo " . "cstPIS " . "Não Existe!" ;
                break;
            }
           
            if(!isset($item->cstCOFINS)){
                return "Campo " . "cstCOFINS " . "Não Existe!" ;
                break;
            }
            if(!isset($item->orig)){
                return "Campo " . "orig " . "Não Existe!" ;
                break;
            }
            
            if(!isset($item->CSOSN)){
                return "Campo " . "CSOSN " . "Não Existe!" ;
                break;
            }
            
           
            if($item->cProd==""){
               return "cProd - Código do produto ou serviço";
                break;
            }
            
            if($item->cEAN==""){
                return "cEAN - GTIN (Global Trade Item Number) do produto, antigo código EAN ou código de barras";
                break;
            }
            if($item->xProd==""){
                return "xProd - Descrição do produto ou serviço";
                break;
            }
            if($item->NCM==""){
                return "NCM - Código NCM com 8 dígitos ou 2 dígitos (gênero)";
                break;
            }
            if($item->CFOP==""){
                return "CFOP - Código Fiscal de Operações e Prestações";
                break;
            }
            if($item->uCom==""){
                return "uCom - Quantidade Comercial do produto";
                break;
            }
            if($item->vUnCom==""){
                return "vUnCom - Valor Unitário de Comercialização do produto";
                break;
            }
            if($item->vProd==""){
                return "vProd - Valor Total Bruto dos Produtos ou Serviços";
                break;
            }
            if($item->cEANTrib==""){
                return "cEANTrib - GTIN (Global Trade Item Number) do produto, antigo código EAN ou código de barras";
                break;
            }
            if($item->uTrib==""){
                return "uTrib - Unidade Tributável do produto";
                break;
            }
            if($item->qTrib==""){
                return "qTrib - Quantidade Tributável do produto";
                break;
            }
            if($item->vUnTrib==""){
                return "vUnTrib - Valor Unitário de tributação do produto";
                break;
            }
            if(($item->indTot=="" || $item->indTot== null ) ||  (intval($item->indTot) != 0 && $item->indTot != 1)){
                return "indTot - Indica se valor do Item (vProd) entra no valor total da NF-e (vProd)";
                break;
            }          
         }
    }
}


