<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaCompartilhada extends Model
{
    use HasFactory;

    protected $table = 'empresas_compartilhadas';
    protected $fillable = [
        'empresa_id',
        'solicitante_user_id',
        'proprietario_user_id',
        'autorizado',
    ];

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'id');
    }

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'proprietario_user_id', 'id');
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_user_id', 'id');
    }
}
