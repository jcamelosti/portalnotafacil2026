<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaNbs extends Model
{
    use HasFactory;

    protected $table = 'empresa_nbs';
      
    protected $fillable = [
       'empresa_id',
       'correlaca_trib_id',
       'codigo',
       'descricao',
    ];

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where(
            $query->getModel()->getTable() . '.empresa_id',
            $empresaId
        );
    }

    public function correlacaoTrib(){
        return $this->belongsTo(CorrelacaoTribMunTribNac::class, 'correlaca_trib_id', 'id');
    }
}
