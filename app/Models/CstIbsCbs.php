<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class CstIbsCbs extends Model
{
    use HasFactory;

    protected $table = 'cst_ibs_cbs';

    protected $fillable = [
        'codigo',
        'descricao',
        'vigencia_inicio',
        'vigencia_fim',
        'ativo',

        // Indicadores
        'ind_gibs_cbs',
        'ind_gibscbs_mono',
        'ind_gred',
        'ind_gdif',
        'ind_gtransf_cred',
        'ind_gcred_pres_ibs_zfm',
        'ind_gajuste_compet',
        'ind_redutor_bc',
    ];

    protected $casts = [
        'vigencia_inicio' => 'date',
        'vigencia_fim' => 'date',
        'ativo' => 'boolean',

        // Indicadores
        'ind_gibs_cbs' => 'boolean',
        'ind_gibscbs_mono' => 'boolean',
        'ind_gred' => 'boolean',
        'ind_gdif' => 'boolean',
        'ind_gtransf_cred' => 'boolean',
        'ind_gcred_pres_ibs_zfm' => 'boolean',
        'ind_gajuste_compet' => 'boolean',
        'ind_redutor_bc' => 'boolean',
    ];

    /**
     * Classificações tributárias vinculadas ao CST.
     */
    public function classificacoesTributarias(): HasMany
    {
        return $this->hasMany(
            ClassificacaoTributaria::class,
            'cst_id'
        );
    }

    /**
     * Somente CSTs ativos.
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Busca pelo código CST.
     */
    public function scopeCodigo($query, string $codigo)
    {
        return $query->where('codigo', $codigo);
    }

    public function listar(){
        return ['' =>'Selecione'] + $this->select(
                'codigo',
                DB::raw("concat(codigo, ' - ', IFNULL(descricao, '')) as field1")
            )
            ->orderBy('codigo', 'asc')
            ->pluck('field1', 'codigo')
            ->all();
    }
}