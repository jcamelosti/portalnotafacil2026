<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tomador;
use Illuminate\Http\Request;

class TomadorController extends Controller
{
    public function buscar(Request $request)
    {
        $q = $request->q;
        $empresas = Tomador::query()
            ->when($q, function($query) use ($q){
                $query->where('razao_social','like',"%{$q}%")
                      ->orWhere('cpf_cnpj','like',"%{$q}%");
            })
            ->limit(10)
            ->get(['id','razao_social','cpf_cnpj']);

        
        /*return response()->json(
            $empresas->map(function($empresa){
                return [
                    'id'   => $empresa->id,
                    'nome' => $empresa->razao_social,
                    'cpf_cnpj' => $empresa->cpf_cnpj
                ];
            })
        );*/

         $dados = $empresas->map(function($empresa){
            return [
                'id'   => $empresa->id,
                'nome' => $empresa->razao_social,
                'cpf_cnpj' => $empresa->cpf_cnpj
            ];

        });

        return response()->json($dados,200,[],JSON_UNESCAPED_UNICODE);
    }    
}
