<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Models\Nbs;
use App\Models\Tomador;
use App\Models\Uf;
use Exception;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    private $municipioModel;
    private $listaServicoModel;
    private $cnaeModel;
    //private $estadosModel;
    private $nbsModel;

    public function __construct(
        //Uf $estadosModel,
        Municipio $municipioModel,
    )
    {
        //$this->estadosModel = $estadosModel;
        $this->municipioModel = $municipioModel;
        //$this->nbsModel = $nbsModel;
    }

    public function consultarCidades($uf_id)
    {
        $referer = request()->headers->get('referer');

        if ($referer && str_contains($referer, '/area-cliente/empresas')) {
            $cidades = $this->municipioModel->municipiosComEndPoint($uf_id);
        }else{
            $cidades = $this->municipioModel->municipios($uf_id);
        }
        
        
        return view('components.cidades', compact('cidades'));
    }

    public function index(){
        return view('novo.index');
    }

    public function consultarNbs($item_lc){
        $nbs_list = $this->nbsModel->getListaNbs($item_lc);
        return view('utils.nbs_list')->with([
            'nbs_list' => $nbs_list
        ]);
    }

    public function buscarCidades(Request $request)
    {
        $q = $request->q;
        $dados = [];
        
        $municipios = Municipio::query()
            ->leftJoin('ufs', 'ufs.id', '=', 'municipios_ibge.uf_id')
            ->when($q, function($query) use ($q){
                $query->where('municipio','like',"%{$q}%")
                      ->orWhere('codigo','like',"%{$q}%");
            })
            ->limit(10)
            ->get([
                'codigo',
                'municipio',
                'ufs.sigla as estado'
            ]);

         $dados = $municipios->map(function($municipio){
            return [
                'id'   => $municipio->codigo,
                'municipio' => $municipio->municipio . '-'. $municipio->estado,
            ];

        });

        return response()->json($dados,200,[],JSON_UNESCAPED_UNICODE);
    }    

    public function buscarCidadesJson(Request $request, $uf_id)
    {        
        $municipios = Municipio::query()
            ->where('uf_id', $uf_id)
            ->get([
                'codigo',
                'municipio',
            ]);

        return response()->json($municipios,200,[],JSON_UNESCAPED_UNICODE);
    }    
}
