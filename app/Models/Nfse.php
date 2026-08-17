<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nfse extends Model
{
    use HasFactory;

    protected $table = 'nfse_emitidas';
    
    protected $fillable = [
        'empresa_id',
        'tomador_id',
        'nfse_id',
        'integracao_id',
        'data_emissao_br',
        'situacao',
        'numero_nfse',
        'numero_rps',
        'codigo_verificacao',
        'mensagem',
        'url_pdf',
    ];

    public function tomador(){
        return $this->belongsTo(Tomador::class);
    }
}
