<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoFiscalRecebido extends Model
{
    use HasFactory;

     protected $fillable = [
        'empresa_id',
        'nsu',
        'tipo_documento',
        'xml',
        'dados',
        'doc_prestador',
        'razao_social',
        'data_emissao_nfse'
    ];

    protected $casts = [
        'nsu' => 'integer',
        'dados' => 'array',
        'data_emissao_nfse' => 'date'
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
