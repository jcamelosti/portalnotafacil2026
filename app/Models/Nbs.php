<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Nbs extends Model
{
    protected $table = 'nbs';

    protected $fillable = [
        'codigo_nbs',
        'descricao_nbs',
        'codigo_subitem_lc',
        'codigo_subitem_lc_limpo'
    ];

    public function getListaNbs($filtro = null)
    {
        return ['' => 'Selecione o NBS'] + $this
            ->select(
                'id',
                DB::raw("concat(codigo_nbs, ' - ', IFNULL(descricao_nbs, '')) as field1")
            )
            ->where(function ($query) use ($filtro) {
                if($filtro != ''){
                    $query->whereIn('codigo_subitem_lc_limpo', [$filtro]);
                }
            })
            ->pluck('field1', 'id')
            ->all();
    }
}
