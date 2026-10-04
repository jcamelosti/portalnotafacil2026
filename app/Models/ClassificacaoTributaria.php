<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassificacaoTributaria extends Model
{
    use HasFactory;

    protected $table = 'classificacoes_tributarias';

    protected $fillable = [
        'codigo',
        'descricao',
        'cst_id',
        'vigencia_inicio',
        'vigencia_fim',
        'ativo',
    ];

    protected $casts = [
        'vigencia_inicio' => 'date',
        'vigencia_fim' => 'date',
        'ativo' => 'boolean',
    ];

    /**
     * CST IBS/CBS relacionado.
     */
    public function cst(): BelongsTo
    {
        return $this->belongsTo(
            CstIbsCbs::class,
            'cst_id'
        );
    }

    /**
     * Retorna somente classificações ativas.
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Busca uma classificação pelo código cClassTrib.
     */
    public function scopeCodigo($query, string $codigo)
    {
        return $query->where('codigo', $codigo);
    }
}