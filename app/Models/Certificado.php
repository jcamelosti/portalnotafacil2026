<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Certificado extends Model
{
    use HasFactory;

    protected $table = 'certificados';
    protected $casts = [
        'data_validade' => 'date',
    ];
    
    protected $fillable = [
        'user_id',
        'empresa_id',
        'razao_social',
        'senha',
        'data_validade',
        'arquivo'
    ];

    public function getVencimentoValidoAttribute()
    {
       return (new \DateTime($this->data_validade)) >= new \DateTime();
    }
}
