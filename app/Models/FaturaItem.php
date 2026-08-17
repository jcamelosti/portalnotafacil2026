<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaturaItem extends Model
{
    use HasFactory;

    protected $table = 'fatura_itens';
    protected $fillable = [
        'plano_id',
        'variacao_plano_id',
        'fatura_id',
        'valor',
        'qtd'
    ];

    public function plano(){
        return $this->belongsTo(Plano::class);
    }
    
    public function variacaoPlano(){
        return $this->belongsTo(PlanoVariacao::class);
    }
    
    public function fatura(){
        return $this->belongsTo(Fatura::class);
    }
}
