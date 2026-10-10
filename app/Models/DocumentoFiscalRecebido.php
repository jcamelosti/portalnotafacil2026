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
    ];

    protected $casts = [
        'nsu' => 'integer',
        'dados' => 'array',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
