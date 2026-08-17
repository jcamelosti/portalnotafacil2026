<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanoVariacao extends Model
{
    use HasFactory;

    protected $table = 'plano_variacoes';
    protected $fillable = [
        'plano_id',
        'descricao',
        'valor',
        'fator_vigencia',
    ];

    public function getValorPlanoFmtAttribute($value)
    {
        return number_format($this->attributes['valor'], 2, ',', '.');
    }

    public function getValorParteAttribute()
    {
        $data = explode('.', $this->attributes['valor']);
        return $data;
    }

    //Accessor - Na Visualização
    public function getModalidadeAttribute($value)
    {
        $text = "mês";
        switch ($this->fator_vigencia) {
            case 30:
                $mes = 'Mensais';
                break;
            case 90:
                $mes = 'Trimestrais';
                break;
            case 180:
                $mes = 'Semestrais';
                break;
            case 365:
                $mes = 'Anuais';
                break;
            default:
                $mes = 'Mensais';
        }
        return $mes;
    }

    public function plano(){
        return $this->belongsTo(Plano::class);
    }
}
