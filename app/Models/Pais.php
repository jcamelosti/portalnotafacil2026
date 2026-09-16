<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pais extends Model
{
    use HasFactory;

    protected $table = 'paises';

    protected $fillable = [
        'codigo',
        'nome',
    ];

    protected $casts = [
        'codigo' => 'string',
        'nome' => 'string',
    ];

     public function paises($ufId = ''){
        return ['' =>'Selecione o Pais'] + $this
            ->orderBy('codigo', 'asc')
            ->pluck('nome', 'codigo')
            ->all();
    }
}