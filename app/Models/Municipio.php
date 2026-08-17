<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Municipio extends Model
{
    protected $table = 'municipios_ibge';
    protected $fillable = [
        'codigo',
        'codigo6digi',
        'municipio',
        'uf_id'
    ];

    public function estado()
    {
        return $this->belongsTo(Uf::class, 'uf_id', 'id');
    }

    public function municipios($ufId = ''){
        return [0 =>'Selecione o Municipio'] + $this
            ->orderBy('municipio', 'asc')
            ->where(function ($query) use ($ufId) {
                if($ufId != ''){
                    $query->where('uf_id', '=', $ufId);
                }
            })
            ->pluck('municipio', 'codigo')
            ->all();
    }

    public function municipiosComEndPoint($ufId = ''){
        return [0 =>'Selecione o Municipio'] + $this
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('endpoints')
                    ->whereColumn('endpoints.codigo_municipio', 'municipios_ibge.codigo');
            })
            ->orderBy('municipio', 'asc')
            ->where(function ($query) use ($ufId) {
                if($ufId != ''){
                    $query->where('uf_id', '=', $ufId);
                }
            })
            ->pluck('municipio', 'codigo')
            ->all();
    }
}