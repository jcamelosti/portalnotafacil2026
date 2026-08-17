<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Protocolo extends Model
{
    use HasFactory;

    protected $table = 'protocolos';

    protected $fillable = [
        'empresa_id',
        'tomador_id',
        'num_nfse',
        'protocolo',
        'descricao_servico',
        'valor_liquido',
        'valor_total',
        'mensagem',
        'tentativas'
    ];

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'id');
    }

    public function tomador()
    {
        return $this->belongsTo(Tomador::class);
    }

    public function getValorLiquidoAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function getValorTotalAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }
}
