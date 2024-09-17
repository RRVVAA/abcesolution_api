<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GradeProduto extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'empresa_id',
        'produto_id',
        'variacao_grade_linha_id',
        'linha_id',
        'variacao_grade_coluna_id',
        'coluna_id',
        'descricao',
        'codigo_barra',
        'estoque',
        'iniciou_estoque'
    ];
    
    public function produto(){
        return $this->belongsTo(Produto::class,"produto_id","id");
    }
    
    public function linha(){
        return $this->belongsTo(ItemVariacaoGrade::class,"linha_id","id");
    }
    
    public function variacao_linha(){
        return $this->belongsTo(VariacaoGrade::class,"variacao_grade_linha_id","id");
    }
    
    public function variacao_coluna(){
        return $this->belongsTo(VariacaoGrade::class,"variacao_grade_coluna_id","id");
    }
    
    public function coluna(){
        return $this->belongsTo(ItemVariacaoGrade::class,"coluna_id","id");
    }
    
    public static function atualizarEstoque($grade_id){
        $entradas   = GradeMovimento::where(["grade_id" => $grade_id, "ent_sai"=>"E"])->where("tipo_movimento_id", "!=", config("constantes.tipo_movimento.SEM_MOVIMENTO"))->sum("qtde_movimento");
        $saidas     = GradeMovimento::where(["grade_id" => $grade_id, "ent_sai"=>"S"])->where("tipo_movimento_id", "!=", config("constantes.tipo_movimento.SEM_MOVIMENTO"))->sum("qtde_movimento");
        $saldo      = $entradas - $saidas;
        
        $todas_entradas   = GradeMovimento::where(["grade_id" => $grade_id, "ent_sai"=>"E"])->sum("qtde_movimento");
        $todas_saidas     = GradeMovimento::where(["grade_id" => $grade_id, "ent_sai"=>"S"])->sum("qtde_movimento");
        $saldo_temp      = $todas_entradas - $todas_saidas;
        
        
        //Atualiza a grade
        $grade = GradeProduto::find($grade_id);
        $grade->estoque= $saldo;
        $grade->estoque_temporario= $saldo_temp;        
        $grade->save();
        
        //atualizo o estoque
        $estoque_grade      = GradeProduto::where("produto_id", $grade->produto_id)->sum("estoque");
        $estoque_grade_temp = GradeProduto::where("produto_id", $grade->produto_id)->sum("estoque_temporario");
        $estoque            = Estoque::where("produto_id", $grade->produto_id)->first();
        if($estoque){
            $estoque->quantidade      = $estoque_grade;
            $estoque->qtde_grade      = $estoque_grade;
            $estoque->qtde_grade_temp = $estoque_grade_temp;
            $estoque->save();
        }else{
            $estoque                  = new Estoque();
            $estoque->produto_id      = $grade->produto_id;
            $estoque->quantidade      = $estoque_grade;
            $estoque->qtde_grade      = $estoque_grade;
            $estoque->qtde_grade_temp = $estoque_grade_temp;
            $estoque->save();
        }
        
        
    }
}
