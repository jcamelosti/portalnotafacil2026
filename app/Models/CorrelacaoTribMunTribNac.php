<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CorrelacaoTribMunTribNac extends Model
{
    use HasFactory;

    protected $table = 'correlacao_trib_mun_trib_nac';
      
    protected $fillable = [
       'empresa_id',
       'cTribMun',
       'xTribMun',
       'aliquota',
       'cTribNac',
       'xTribNac',
    ];

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where(
            $query->getModel()->getTable() . '.empresa_id',
            $empresaId
        );
    }

    public function atividade()
    {
        return $this->belongsTo(
            EmpresaAtividade::class,
            'cTribMun',
            'codigo_atividade'
        );
    }

    public function listCorrelacao($empresaId){
        return [null =>'Selecione a Cód. Tributação Nacional'] + $this
            ->select(
                'id',
                DB::raw("concat(cTribNac, ' - ', IFNULL(xTribNac, '')) as field1")
            )
            ->where('empresa_id', $empresaId)
            ->orderBy('id', 'asc')
            ->pluck('field1', 'id')
            ->all();
    }
}
