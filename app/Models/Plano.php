<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    use HasFactory;

    protected $table = 'planos';
    protected $fillable = [
        'plano_nome',
        'plano_descricao',
        'plano_detalhes',
        'exibir_site'
    ];

    //Accessor - Na Visualização
    public function getPlanoDescricao2Attribute($value)
    {
        $data = explode(';', $value);
        return $data;
    }
    
    public function variacoes()
    {
        return $this->hasMany(PlanoVariacao::class);
    }

    public function list(){
        return ['' =>'Selecione o Plano'] + $this
            ->orderBy('id', 'desc')
            ->pluck('plano_nome', 'id')
            ->all();
    }
}
