<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class EmpresaAtividade extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'empresa_atividades';
    protected $fillable = [
        'empresa_id',
        'codigo_atividade',
        'descricao_atividade',
        'vigencia_inicial',
        'vigencia_final',
        'aliquota',
    ];
    protected function found($empresa_id, $codigo_atividade){
        $res = $this
            ->where('empresa_id', $empresa_id)
            ->where('codigo_atividade', $codigo_atividade)
            ->first();
        if($res){
            return $res;
        }
        return false;
    }
    public function insere($dados){
        if(!$this->found($dados['empresa_id'], $dados['codigo_atividade'])){
            $this->create($dados);
        }else{
            $rec = $this->found($dados['empresa_id'], $dados['codigo_atividade']);
            $rec->empresa_id  = $dados['empresa_id'];
            $rec->codigo_atividade = $dados['codigo_atividade'];
            $rec->descricao_atividade = $dados['descricao_atividade'];
            $rec->vigencia_inicial =  $dados['vigencia_inicial'];
            $rec->vigencia_final =  $dados['vigencia_final'];
            $rec->aliquota = $dados['aliquota'];
            $rec->save();
        }
    }

    public function atividadesList($empresaId){
        return [null =>'Selecione a Atividade no Município'] + $this
            ->select(
                'id',
                DB::raw("concat(codigo_atividade, ' - ', IFNULL(descricao_atividade, '')) as field1")
            )
            ->where('empresa_id', $empresaId)
            ->where(function ($query) {
                $query->whereNull('vigencia_final')
                    ->orWhere('vigencia_final', '>=', now());
            })
            ->orderBy('descricao_atividade', 'asc')
            ->pluck('field1', 'id')
            ->all();
    }

    public function atividadesByCTribMunList($empresaId){
        return [null =>'Selecione a Atividade no Município'] + $this
            ->select(
                'codigo_atividade',
                DB::raw("concat(codigo_atividade, ' - ', IFNULL(descricao_atividade, '')) as field1")
            )
            ->where('empresa_id', $empresaId)
            ->where(function ($query) {
                /*$query->whereNull('vigencia_final')
                    ->orWhere('vigencia_final', '>=', now());*/
            })
            ->orderBy('descricao_atividade', 'asc')
            ->pluck('field1', 'codigo_atividade')
            ->all();
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where(
            $query->getModel()->getTable() . '.empresa_id',
            $empresaId
        );
    }
}
