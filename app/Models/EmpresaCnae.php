<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class EmpresaCnae extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'empresa_cnaes';
    protected $fillable = [
        'empresa_id',
        'codigo_cnae',
        'descricao_cnae',
        'principal',
    ];
    protected function found($empresa_id, $codigo_cnae){
        $res = $this
            ->where('empresa_id', $empresa_id)
            ->where('codigo_cnae', $codigo_cnae)
            ->first();
        if($res){
            return $res;
        }
        return false;
    }
    public function insere($dados){
        if(!$this->found($dados['empresa_id'], $dados['codigo_cnae'])){
            $this->create($dados);
        }else{
            $rec = $this->found($dados['empresa_id'], $dados['codigo_cnae']);
            $rec->empresa_id = $dados['empresa_id'];
            $rec->codigo_cnae = $dados['codigo_cnae'];
            if(isset($dados['descricao_cnae'])){
                $rec->descricao_cnae = $dados['descricao_cnae'];
            }
            if(isset($dados['principal'])){
                $rec->principal = $dados['principal'];
            }
            $rec->save();
        }
    }

    public function cnaesList($empresaId){
        return [0 =>'Selecione o CNAE'] + $this
            ->select(
                'id',
                DB::raw("concat(codigo_cnae, ' - ', IFNULL(descricao_cnae, '')) as field1")
            )
            ->where('empresa_id', $empresaId)
            ->orderBy('descricao_cnae', 'asc')
            ->pluck('field1', 'id')
            ->all();
    }

}
