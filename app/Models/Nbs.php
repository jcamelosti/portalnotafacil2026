<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Nbs extends Model
{
    protected $table = 'nbs';

    protected $fillable = [
        'codigo',
        'descricao',
    ];

    public function getListaNbs()
    {
        return ['' => 'Selecione o NBS'] + $this
            ->select(
                'id',
                DB::raw("concat(codigo, ' - ', IFNULL(descricao, '')) as field1")
            )
            /*->where(function ($query) use ($filtro) {
                if($filtro != ''){
                    $query->whereIn('codigo_subitem_lc_limpo', [$filtro]);
                }
            })*/
            ->pluck('field1', 'id')
            ->all();
    }

    public function getListaNbsPorCodigo()
    {
        return ['' => 'Selecione o NBS'] + $this
            ->select(
                'codigo',
                DB::raw("concat(codigo, ' - ', IFNULL(descricao, '')) as field1")
            )
            /*->where(function ($query) use ($filtro) {
                if($filtro != ''){
                    $query->whereIn('codigo_subitem_lc_limpo', [$filtro]);
                }
            })*/
            ->pluck('field1', 'codigo')
            ->all();
    }
}
