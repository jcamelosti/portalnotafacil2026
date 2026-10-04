<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IndOpIbsCbs extends Model
{
    use HasFactory;

    protected $table = 'ind_op_ibs_cbs';

    protected $fillable = [
        'codigo',
        'descricao',
        'local_operacao',
        'local_fornecimento',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * Scope para buscar somente registros ativos.
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para buscar pelo código.
     */
    public function scopeCodigo($query, string $codigo)
    {
        return $query->where('codigo', $codigo);
    }

    public function indicadorOperacoes(){
        return ['' =>'Selecione'] + $this->select(
                'codigo',
                DB::raw("concat(codigo, ' - ', IFNULL(descricao, '')) as field1")
            )
            ->orderBy('descricao', 'asc')
            ->pluck('field1', 'codigo')
            ->all();
    }
}