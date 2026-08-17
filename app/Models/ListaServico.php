<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ListaServico extends Model
{
    protected $table = 'lista_servicos';

    protected $fillable = [
        'id',
        'ordem',
        'ordem2',
        'descricao', 
        'item_lc',
    ];

    public function getListaServicos($filtro = null)
    {
        return ['' => 'Selecione o Item LC'] + $this
            ->where(function ($query) use ($filtro) {
                if($filtro != ''){
                    $query->whereIn('item_lc', $filtro);
                }
            })
            ->orderBy('ordem', 'asc')
            ->orderBy('ordem2', 'asc')
            ->orderBy('id', 'asc')
            ->pluck('descricao', 'id')
            ->all();
    }
}
