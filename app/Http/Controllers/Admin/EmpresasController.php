<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CnaeLc;
use Illuminate\Http\Request;

use App\Models\Empresa;
use App\Models\EmpresaAtividade;
use App\Models\EmpresaCnae;
use App\Models\Fatura;
use App\Models\FaturaItem;
use App\Models\License;
use App\Models\ListaServico;
use App\Models\Municipio;
use App\Models\Nbs;
use App\Models\NotaEmitida;
use App\Models\Plano;
use App\Models\Tomador;
use App\Models\Uf;
use App\Models\User;
use App\Utilitarios\Utilitarios;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


//Final necessário para Emissao

class EmpresasController extends Controller
{
    private $empresaModel;
    private $estadoModel;
    private $municipioModel;
    private $listaServicoModel;
    private $cnaeModel;
    private $atividadeModel;
    private $licenseModel;
    private $planoModel;
    private $userModel;
    private $nbsModel;

    public function __construct(
        Empresa $empresaModel, Uf $estadoModel, 
        Municipio $municipioModel, ListaServico $listaServicoModel,
        EmpresaCnae $cnaeModel, EmpresaAtividade $atividadeModel,
        License $licenseModel,
        Plano $planoModel,
        User $userModel,
        Nbs $nbsModel
    )
    {
        $this->empresaModel = $empresaModel;
        $this->estadoModel = $estadoModel;
        $this->municipioModel = $municipioModel;
        $this->listaServicoModel = $listaServicoModel;
        $this->cnaeModel = $cnaeModel;
        $this->atividadeModel = $atividadeModel;
        $this->licenseModel = $licenseModel;
        $this->planoModel = $planoModel;
        $this->userModel = $userModel;
        $this->nbsModel = $nbsModel;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $empresasList = $this->empresaModel
            ->empresasList();
         
        $campos = $request->all();

        $empresas = $this->empresaModel
            ->orderBy('razao_social', 'ASC')
            ->where(function($query) use($campos) {
                if(isset($campos['empresa_id']) && $campos['empresa_id'] != '0'){
                    $query->where('id', $campos['empresa_id']);
                }
                if(isset($campos['cidade_id']) && $campos['cidade_id'] != ''){
                    $query->where('cidade_id', $campos['cidade_id']);
                }
            })
            ->paginate(20)->withQueryString();

        /*$cidades = Municipio::whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('endpoints')
                    ->whereColumn('endpoints.codigo_municipio', 'municipios_ibge.codigo');
            })
            ->orderBy('municipio', 'asc')
            ->pluck('municipio', 'codigo')
            ->all();*/

        $cidades = Municipio::query()
            ->leftJoin(
                'empresas',
                'empresas.cidade_id',
                '=',
                'municipios_ibge.codigo'
            )
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('endpoints')
                    ->whereColumn(
                        'endpoints.codigo_municipio',
                        'municipios_ibge.codigo'
                    );
            })
            ->select(
                'municipios_ibge.codigo',
                'municipios_ibge.municipio',
                DB::raw('COUNT(empresas.id) as total_empresas')
            )
            ->groupBy(
                'municipios_ibge.codigo',
                'municipios_ibge.municipio'
            )
            ->orderBy('municipios_ibge.municipio', 'asc')
            ->get();
                  
        return view('admin.empresas.index')->with([
            'empresas' => $empresas,
            'empresasList' => $empresasList,
            'cidades' => $cidades
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $estados = $this->estadoModel->getListaEstados();
        $cidades = $this->municipioModel->municipios();
        $servicos = $this->listaServicoModel->getListaServicos();

        return view('admin.empresas.inserir')->with([
            'empresa' => new Empresa(),
            'uf_id' => 9,
            'estados' => $estados,
            'cidades' => $cidades,
            'servicos' => $servicos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cnpj = preg_replace('/[^0-9]/', '', $request->input('cpf_cnpj'));

        $dadosEmpresa = Utilitarios::consultarEmpresaCNPJ($cnpj);
        $cadastroExiste = $this->empresaModel->where('cpf_cnpj', $cnpj)->first();

        if(is_null($cadastroExiste)){
            $empresaAdd = $this->empresaModel->fill($dadosEmpresa);
            $empresaAdd->user_id = 1;
            $empresaAdd->inscricao_municipal = '00000';
            $empresaAdd->save();

            License::create([
                'empresa_id' => $empresaAdd->id,
                'validate' => Carbon::now()->subDay(1)->format('Y-m-d')
            ]);            

            session()->flash('message', 'Empresa cadastrada com sucesso.');
            return redirect()->route('admin.empresas.edit', $empresaAdd->id);
        }else{
            session()->flash('message', 'Empresa já Cadastrada.');
            return redirect()->route('admin.empresas.edit', $cadastroExiste->id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $empresa = $this->empresaModel
            ->with(['cnaes'])
            ->find($id);
        
        $estados = $this->estadoModel->getListaEstados();
        $cidade = $empresa->cidade()->first();
        $uf_id = $empresa->cidade()->first()->estado()->first()->id;
        $cidades = $this->municipioModel->municipios($uf_id);
        $planos = $this->planoModel->list();
        $usuarios = $this->userModel->list();

        $cnaes = $this->cnaeModel->cnaesList($empresa->id);
        $atividades = $this->atividadeModel->atividadesList($empresa->id);
                
        $empresaCnaePrincipalId = $empresa->empresa_cnae_id;
        
        //filtro items lc conforme cnae
        $listaCnae = $this->cnaeModel->find($empresaCnaePrincipalId);
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

        if(!empty($empresa->item_lc_id)){
            $nbs_list = $this->nbsModel->getListaNbs($empresa->item_lc_id);
        }else{
            $nbs_list = [];
        }

        $regimeEspecialTributacaoList = $this->empresaModel->getRegimeEspecialTributacao();

        $provedores = Empresa::getProvedorEmissao();
        $ambientes_emissao = Empresa::getAmbienteEmissao();

        $regimes_tributarios = Empresa::getRegimeTributario();//Situação perante Simples Nacional, preenche campo opSimpNac na NFSE em regTrib
        $situacao_simples_nacional = Empresa::getOpcaoSimplesNacional();//Regime de Apuração Tributária pelo Simples Nacional, campo regApTribSN em regTrib
        $tipos_regime_esp_trib_mun = Empresa::getTiposRegimeEspecialTributacaoMunicipio();

        return view('admin.empresas.editar')->with([
            'estados' => $estados,
            'empresa' => $empresa,
            'uf_id' => $uf_id,
            'cidade' => $cidade,
            'cidades' => $cidades,
            'servicos' => $servicos,
            'atividades' => $atividades,
            'cnaes' => $cnaes,
            'planos' => $planos,
            'usuarios' => $usuarios,
            'reg_esp_trib' => $regimeEspecialTributacaoList,
            'nbs_list' => $nbs_list,
            'provedores' => $provedores,
            'ambientes_emissao' => $ambientes_emissao,
            'regimes_tributarios' => $regimes_tributarios,
            'situacao_simples_nacional' => $situacao_simples_nacional,
            'tipos_regime_esp_trib_mun' => $tipos_regime_esp_trib_mun,           
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $dados = $request->all();
        
        $empresa = $this->empresaModel->find($id);     
        switch($empresa->cidade_id):
            case '5208707'://goiania
                $dados['serie_nota'] = 1;
                break;
            case '5201405'://aparecidada de goiania
                $dados['serie_nota'] = 9;
                break;
            case '3301702'://duque de caxias
                $dados['serie_nota'] = 1;
                break;
            case '3543402'://Ribeirão Preto
                $dados['serie_nota'] = 1;
                break;
            default:
                $dados['serie_nota'] = 8;
                break;
        endswitch;  
        $empresa->update($dados);

        session()->flash('message', 'Registro Atualizado com Sucesso.');
        return redirect()->route('admin.empresas.edit', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $empresa = $this->empresaModel->find($id);
            $cnaes = $this->cnaeModel->where('empresa_id', $id)->get();
            $atividades = $this->atividadeModel->where('empresa_id', $id)->get();
            $licenca = $this->licenseModel->where('empresa_id', $id)->get();
            $tomadores = Tomador::where('empresa_id', $id)->get();
            $notas = NotaEmitida::where('empresa_id', $id)->get();
        
            /*foreach($notas as $n){
                $n->delete();
            }

            foreach($tomadores as $t){
                $t->delete();
            }

            foreach($cnaes as $cnae){
                $cnae->delete();
            }

            foreach($atividades as $atv){
                $atv->delete();
            }
            
            foreach($licenca as $l){
                $l->delete();
            }

            $faturas = Fatura::where('empresa_id', $empresa->id)->get();
            
            foreach ($faturas as $fat) {
                FaturaItem::where('fatura_id', $fat->id)->delete();
            }

            Fatura::where('empresa_id', $empresa->id)->delete();

            $empresa->delete();*/
        }catch(\Exception $e){
            dd($e->getMessage() . ' ' . $e->getLine());
        }

        session()->flash('message', 'Registro Removido com Sucesso.');

        return redirect()->route('admin.empresas.index');
    }

    public function atualizarRegimeEspecial($id)
    {
        $empresa = $this->empresaModel->find($id);
        /*
         const REGIME_MICROEMPRESA = 1;
            const REGIME_ESTIMATIVA = 2;
            const REGIME_SOCIEDADE = 3;
            const REGIME_COOPERATIVA = 4;
            const REGIME_MEI = 5;
            const REGIME_ME_EPP = 6;
        */
        $dados = Utilitarios::consultarEmpresaCNPJ($empresa->cpf_cnpj);
        
        $empresa->porte_empresa = $dados['porte'];
        $empresa->natureza_juridica = $dados[''] = $dados['natureza_juridica'];
        $empresa->save();
        return redirect()->route('admin.empresas.edit', $id);
    }

    public function removerDados($id){
        $empresa = $this->empresaModel->find($id);
        return view('admin.empresas.remover-dados')->with([
            'empresa' => $empresa
        ]);
    }

    public function empresasPorEstado(Request $request){
        /*$resultado = Empresa::query()
            ->leftJoin('municipios_ibge as mi', 'mi.codigo', '=', 'empresas.cidade_id')
            ->leftJoin('ufs as uf', 'uf.id', '=', 'mi.uf_id')
            ->selectRaw('
                COUNT(empresas.cidade_id) as total,
                empresas.cidade_id,
                mi.municipio,
                uf.sigla
            ')
            ->groupBy(
                'empresas.cidade_id',
                'mi.municipio',
                'uf.sigla'
            )
            ->orderBy('mi.municipio')
            ->get();*/
        
        $resultado = Empresa::query()
            ->leftJoin('municipios_ibge as mi', 'mi.codigo', '=', 'empresas.cidade_id')
            ->leftJoin('ufs as uf', 'uf.id', '=', 'mi.uf_id')
            ->leftJoin('emitidas as e', 'e.empresa_id', '=', 'empresas.id')
            ->selectRaw("
                COUNT(DISTINCT empresas.id) as total,
                empresas.cidade_id,
                mi.municipio,
                uf.sigla,
                MAX(e.created_at) as ultima_emissao
            ")
            ->groupBy(
                'empresas.cidade_id',
                'mi.municipio',
                'uf.sigla'
            )
            ->orderBy('ultima_emissao', 'desc')
            ->orderBy('mi.municipio', 'asc')
            ->get();

        return view('admin.empresas.empresas-por-estado')->with([
            'resultado' => $resultado,
            'totalGeral' => $resultado->sum('total')
        ]);
    }
}
