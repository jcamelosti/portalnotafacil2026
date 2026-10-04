<?php

namespace App\Http\Controllers\Emissor;

use App\Http\Controllers\Controller;
use App\Models\NotaEmitida;
use Mpdf\Mpdf;
use App\Services\Nfse\DanfseXmlParser;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DanfseController extends Controller
{
    private $empresaModel;

    public function __construct()
    {
        $this->empresaModel = app()->make('App\Models\Empresa');
    }

    public function pdf(
        NotaEmitida $nota,
        DanfseXmlParser $parser
    ) {
        $empresa_sessao = session('empresa');
        
        if(Auth::user()->is_admin != 1){
            if(is_null($empresa_sessao)){
                abort(Response::HTTP_FORBIDDEN, 'Você não tem permissão para acessar esta nota.');
            }else{
                $empresaCompartilhada = $this->empresaModel
                    ->where('empresas.id', $nota->empresa_id)
                    ->where(function ($query) {
                        $query->where('user_id', Auth::id())
                            ->orWhereExists(function ($sub) {
                                $sub->select(DB::raw(1))
                                    ->from('empresas_compartilhadas')
                                    ->whereColumn(
                                        'empresas_compartilhadas.empresa_id',
                                        'empresas.id'
                                    )
                                    ->where(
                                        'empresas_compartilhadas.solicitante_user_id',
                                        Auth::id()
                                    )
                                    ->where(
                                        'empresas_compartilhadas.autorizado',
                                        'S'
                                    );
                            });
                    })
                    ->first();
                
                $mesmaEmpresa = (int) $empresa_sessao->id === (int) $nota->empresa_id;
                $usuarioEhDono = (int) $nota->empresa->user_id === (int) Auth::id();

                if (!$mesmaEmpresa && !$usuarioEhDono && is_null($empresaCompartilhada)) {
                    abort(
                        Response::HTTP_FORBIDDEN,
                        'Você não tem permissão para acessar esta nota.'
                    );
                }               
            }
        }

        $data = $parser->parse($nota->nfse_xml);

        return view('nfse.danfse', [
            'data' => $data,
            'nota' => $nota,
        ]);
    }
}
