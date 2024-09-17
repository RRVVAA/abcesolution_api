<?php

namespace App\Http\Controllers;

use App\Models\Produto;

class ProdutoController extends Controller{
    
    public function listaProdutoPorEmpresa($id_empresa){        
        $lista = Produto::where("empresa_id", $id_empresa)->get();
       
        echo json_encode($lista);
    }
    
    public function detalheProduto($id_produto, $id_empresa){
        $produto = Produto::where(["id"=>$id_produto,"empresa_id" =>$id_empresa])->with('tributacao')->first();        
        echo json_encode($produto);
    }
    
    
    public function getProdutoPorCodigo($id_produto, $id_empresa){
        $produto = Produto::where(["id"=>$id_produto,"empresa_id" =>$id_empresa])->with('tributacao')->first();       
        echo json_encode($produto);
    }
    
    public function listaProdutoPorCategoria($id_categoria, $id_empresa){
        $produtos = Produto::where(["categoria_id"=>$id_categoria,"empresa_id"=>$id_empresa])->get();
        echo json_encode($produtos);
    }
     
    public function listaProdutoPorNome($q, $id_empresa){
        $lista = Produto::where("nome","like","%$q%")->where("empresa_id", $id_empresa)->get();
        echo json_encode($lista);
    }
   
}
