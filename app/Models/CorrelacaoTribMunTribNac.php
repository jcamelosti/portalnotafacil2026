<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


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

    public function atividade(){
        return $this->belongsTo(EmpresaAtividade::class, 'cTribMun' ,'codigo_atividade');
    }
}
