<?php
namespace App\Services;

use App\Models\Estoque;

class EstoqueService{
    public static function existStock($productId){
        $p = Estoque::where('produto_id', $productId)->first();
        return $p != null ? $p : 0;
    }
    
    public static function getStockProduct($productId){
        $stock = self::existStock($productId);
        return $stock->quantity ?? 0;
    }
    
    public static function adicionarEstoque($productId, $qtde){       
        $qtde = (float)$qtde;
        $stock = self::existStock($productId);
        if($stock){ // update
            $stock->quantidade += $qtde;            
        }else{
            $stock = new Estoque();
            $stock->quantidade = $qtde;
            $stock->produto_id = $productId;
        }
        $stock->save();
    }
    
    public static function subtrairEstoque($productId, $qtde){
        $qtde = (float)$qtde;
        $stock = self::existStock($productId);
        if($stock){ // update
            $stock->quantidade -= $qtde;
            if($stock->quantidade < 0.010 ) $stock->quantidade = 0;
            $stock->save();
        }        
    }
    
}

