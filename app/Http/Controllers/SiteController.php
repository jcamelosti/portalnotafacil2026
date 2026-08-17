<?php

namespace App\Http\Controllers;

use App\Models\CnaeLc;
use App\Models\EmpresaCnae;
use App\Models\ListaServico;
use App\Models\Municipio;
use App\Models\Nbs;
use App\Models\NotaEmitida;
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
        ListaServico $listaServicoModel,
        EmpresaCnae $cnaeModel, Nbs $nbsModel
    )
    {
        //$this->estadosModel = $estadosModel;
        $this->municipioModel = $municipioModel;
        $this->listaServicoModel = $listaServicoModel;
        $this->cnaeModel = $cnaeModel;
        $this->nbsModel = $nbsModel;
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

    public function visualizarNota($tomadorCNPJ, $notaId)
    {
        $cnpj = strrev(base64_decode($tomadorCNPJ));
        $notaId = base64_decode($notaId);

        try{
            $nota = NotaEmitida::findOrFail($notaId);
            $tomadorDoc = $nota->tomador->cpf_cnpj;
            if(str_contains($tomadorDoc, $cnpj)){
                return redirect()->to($nota->url_view);
            }else{
                return redirect()->to('https://www.portalnotafacil.com.br/#precos');
            }
        }catch(Exception $e){
            return redirect()->to('https://www.portalnotafacil.com.br/#precos');
        }
    }

    public function ajustes(CnaeLc $cnaeLcModel, ListaServico $listaServicoModel){
        /*$cnaeLcs = $cnaeLcModel->orderBy('descricao_item')
            ->get();*/  

        /*foreach($cnaeLcs as $cnaeLc){
            echo $cnaeLc->descricao_item;
            exit;
            / *$itemServico = $listaServicoModel
                ->where('id', (int)$cnaeLc->item_lc)
                ->first();

            if(!is_null($itemServico)){
                $itemServico->id = (int)$cnaeLc->item_lc;
                $itemServico->descricao = $cnaeLc->descricao_item;
                $itemServico->save();
            }else{
                $listaServicoModel->create([
                    'id' => (int)$cnaeLc->item_lc,
                    'ordem' => 0,
                    'descricao' => utf8_encode($cnaeLc->descricao_item)
                ]);
            }* /
        }*/

        /*$ajustarOrdemItems = $listaServicoModel
            ->get();
             
        foreach($ajustarOrdemItems as $lc){
            $item = explode('-', $lc->descricao);
            $item = trim(substr($item[0],0,5));
            $valor = (float)$item;
            $item = explode('.', $valor);

            if(count($item) == 1){
                $ordem1 = $item[0];
                $ordem2 = 0;

                $lc->ordem = $ordem1;
                $lc->ordem2 = $ordem2;
                $lc->item_lc = (int) $ordem1;
            }else{
                $ordem1 = $item[0];
                $ordem2 = (int) $item[1];

                $lc->ordem = $ordem1;
                $lc->ordem2 = $ordem2;
                $lc->item_lc = str_pad($ordem1, 2, '0', STR_PAD_LEFT).str_pad($ordem2, 2, '0', STR_PAD_LEFT);
            }
            $lc->save();
        }*/
    }

    public function consultarItemLc($cnae_id){
        $listaCnae = $this->cnaeModel->find($cnae_id);
        
        if(!is_null($listaCnae)){
            $filtroLc = CnaeLc::where('cnae', $listaCnae->codigo_cnae)->get();
            $itemLcFiltro = [];
            foreach($filtroLc as $filter){
                $itemLcFiltro[] = $filter->item_lc;
            }
            $servicos = $this->listaServicoModel->getListaServicos($itemLcFiltro);
        }else{
            $servicos = [];
        }

        return view('utils.item_lc', compact('servicos'));
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
}
