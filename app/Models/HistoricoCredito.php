<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoCredito extends Model
{
    use HasFactory;

    protected $table = 'credito_historico';
    
    protected $fillable = [
        'user_id', 'operacao', 'valor'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getValorFmtAttribute($value)
    {
        return number_format($this->attributes['valor'], 2, ',', '.');
    }
}
