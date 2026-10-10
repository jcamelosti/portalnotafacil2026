<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DfeConsulta extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'ult_nsu',
        'max_nsu',
        'status',
        'erro',
        'iniciada_em',
        'finalizada_em',
    ];

    protected function casts(): array
    {
        return [
            'ult_nsu' => 'integer',
            'max_nsu' => 'integer',
            'iniciada_em' => 'datetime',
            'finalizada_em' => 'datetime',
        ];
    }
}
