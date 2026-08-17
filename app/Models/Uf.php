<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Uf extends Model
{
    protected $table = 'ufs';

    public function getListaEstados(){
        return [0 =>'Selecione o Estado'] + $this
            ->orderBy('sigla', 'asc')
            ->pluck('nome', 'id')
            ->all();
    }

    public function getListaEstadosAtendidos(){
        $estadosIds = DB::table("municipios_ibge")
            ->distinct()
            ->select('uf_id')
            ->whereIn('codigo', [
                5201108,
                5201405,
                3169307,
                5002209,
                5003702,
                5007208,
                5007901,
                5103403,
                5106307,
                1500107,
                4104808,
                4306106,
                4313409,
                4316907,
                3502507,
                3524402,
                3541000,
                3551009
            ])
            ->get();

        $filtro = [];
        foreach ($estadosIds as $estadoId){
            $filtro[] = $estadoId->uf_id;
        }

        return [0 =>'Selecione o Estado'] + $this
                ->whereIn('id', $filtro)
                ->orderBy('sigla', 'asc')
                ->pluck('nome', 'id')
                ->all();
    }
}
