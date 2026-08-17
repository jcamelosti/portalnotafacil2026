<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;

    protected $table = 'servicos';

    protected $guarded = ['id'];

    protected $fillable = [
        'empresa_id',
        'fatura_id',
        'empresa_cliente_id',
        'local_prestacao',
        'tomador_id',
        'cod_trib_nacional_id',
        'nbs_id',
        'valor_servico',
        'chave_nfse',
        'data_emissao',
        'descricao'
    ];

    protected $casts = [
        'valor_servico' => 'decimal:2',
        'data_emissao' => 'date'
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'local_prestacao', 'codigo');
    }

    public function empresa_cliente(){
        return $this->belongsTo(Empresa::class, 'empresa_cliente_id', 'id');
    }

    /**
     * Relacionamento com Tomador
     */
    public function tomador()
    {
        return $this->belongsTo(Tomador::class);
    }

    /**
     * Código Tributário Nacional
     */
    public function codigoTributacao()
    {
        return $this->belongsTo(CodigoTribNacional::class, 'cod_trib_nacional_id');
    }

    /**
     * NBS
     */
    public function nbs()
    {
        return $this->belongsTo(Nbs::class);
    }

     public function getValorServicoFmtAttribute($value)
    {
        return number_format($this->attributes['valor_servico'], 2, ',', '.');
    }

    public function fatura(){
        return $this->belongsTo(Fatura::class, 'fatura_id', 'id');
    }
}
