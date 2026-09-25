<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Moeda extends Model
{
    use HasFactory;

    protected $table = 'moedas';

    protected $fillable = [
        'codigo_numerico',
        'nome',
        'simbolo',
        'codigo_pais',
        'pais',
    ];

    protected $casts = [
        'codigo_numerico' => 'string',
        'codigo_pais' => 'string',
    ];

    public function getListaMoedas()
    {
        return ['' => 'Selecione a Moeda'] + $this
            ->select(
                'codigo_numerico',
                DB::raw("concat(codigo_numerico, ' - ', IFNULL(simbolo, ''), ' - ' , IFNULL(pais, '')) as field1")
            )
            /*->where(function ($query) use ($filtro) {
                if($filtro != ''){
                    $query->whereIn('codigo_subitem_lc_limpo', [$filtro]);
                }
            })*/
            ->pluck('field1', 'codigo_numerico')
            ->all();
    }
}