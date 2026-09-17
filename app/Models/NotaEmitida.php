<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaEmitida extends Model
{
    protected $table = 'notas_emitidas';

    protected $fillable = [
        'empresa_id',
        'tomador_id',
        'nfse_xml',
        'num_nfse',
        'valor',
        'dados_emissao',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'dados_emissao' => 'array',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function tomador(): BelongsTo
    {
        return $this->belongsTo(Tomador::class, 'tomador_id');
    }


    public function getCanCancelAttribute(){       
        $diff = now()->diffInDays($this->created_at);
        
        if($diff <= 2){
            return true;
        }

        return false;
    }
}