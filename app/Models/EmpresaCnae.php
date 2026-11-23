<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpresaCnae extends Model
{
    use SoftDeletes;

    public const PRINCIPAL = 1;
    public const SECUNDARIO = 2;

    protected $table = 'empresa_cnaes';

    protected $fillable = [
        'empresa_id',
        'codigo_cnae',
        'descricao_cnae',
        'principal',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'id');
    }
}
