<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CnaeLc;
use Illuminate\Http\Request;

use App\Models\Empresa;
use App\Models\EmpresaAtividade;
use App\Models\EndPoint;
use App\Models\License;
use App\Models\Municipio;
use App\Models\Nbs;
use App\Models\NotaEmitida;
use App\Models\Plano;
use App\Models\Tomador;
use App\Models\Uf;
use App\Models\User;
use App\Services\Nfse\ProvedorNfseService;
use App\Utilitarios\Utilitarios;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    private $provedorNfseService;

    public function __construct(
        Empresa $empresaModel, Uf $estadoModel, 
        Municipio $municipioModel, 
        EmpresaAtividade $atividadeModel,
        License $licenseModel,
        Plano $planoModel,
        User $userModel,
        Nbs $nbsModel,
        ProvedorNfseService $provedorNfseService
    )
    {
        $this->empresaModel = $empresaModel;
        $this->estadoModel = $estadoModel;
        $this->municipioModel = $municipioModel;
        $this->atividadeModel = $atividadeModel;
        $this->licenseModel = $licenseModel;
        $this->planoModel = $planoModel;
        $this->userModel = $userModel;
        $this->nbsModel = $nbsModel;
        $this->provedorNfseService = $provedorNfseService;
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

        return view('admin.empresas.inserir')->with([
            'empresa' => new Empresa(),
            'uf_id' => 9,
            'estados' => $estados,
            'cidades' => $cidades,
            //'servicos' => $servicos
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
            $cidade = $this->municipioModel
                ->leftJoin('ufs', 'ufs.id', '=', 'municipios_ibge.uf_id')
                ->where('municipios_ibge.municipio', $dadosEmpresa['municipio'])
                ->where('ufs.sigla', $dadosEmpresa['uf'])
                ->first();
            
            $empresaAdd = $this->empresaModel->fill($dadosEmpresa);
            $empresaAdd->cidade_id = $cidade->codigo;
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
            //->with(['cnaes'])
            ->find($id);
        
        $estados = $this->estadoModel->getListaEstados();
        $cidade = $empresa->cidade()->first();
        $uf_id = $empresa->cidade()->first()->estado()->first()->id;
        $cidades = $this->municipioModel->municipios($uf_id);
        $planos = $this->planoModel->list();
        $usuarios = $this->userModel->list();
        $atividades = $this->atividadeModel->atividadesList($empresa->id);
        
        $provedores = Empresa::getProvedorEmissao();
        $ambientes_emissao = Empresa::getAmbienteEmissao();

        $situacao_simples_nacional = Empresa::getOpcaoSimplesNacional();//Regime de Apuração Tributária pelo Simples Nacional, campo regApTribSN em regTrib
        //Regime de Apuração Tributária pelo Simples Nacional.
        $regimes_apuracao_sn = Empresa::getRegimeApuracaoSimplesNacional();
        //Tipos de Regimes Especiais de Tributação Municipal:
        $tipos_regime_esp_trib_mun = Empresa::getTiposRegimeEspecialTributacaoMunicipio();

        return view('admin.empresas.editar')->with([
            'estados' => $estados,
            'empresa' => $empresa,
            'uf_id' => $uf_id,
            'cidade' => $cidade,
            'cidades' => $cidades,
            'atividades' => $atividades,
            'planos' => $planos,
            'usuarios' => $usuarios,
            'provedores' => $provedores,
            'ambientes_emissao' => $ambientes_emissao,
            'regimes_apuracao_sn' => $regimes_apuracao_sn,
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
        if($dados['is_optante_simples_nac'] == 1){
            $dados['op_simp_nac'] = 3;
            $dados['regime_tributario'] = 'simples';
            $dados['regime'] = 'simplesNacional';
            
            if($dados['is_mei'] != "0"){
                $dados['op_simp_nac'] = 2;
                $dados['regime_tributario'] = 'mei';
                $dados['regime'] = 'simplesNacionalMEI';
            }
            $dados['regime'] = '-';
        }else{
            $dados['op_simp_nac'] = 1;
            $dados['regime'] = 'regimeNormal';
            $dados['regime_tributario'] = 'normal';
        }

        $empresa->update($dados);

        //$provedor = EndPoint::where('codigo_municipio', $empresa->cidade_id)->first();
        if($empresa->sigla_provedor == 'outros'){
            try {
                $this->provedorNfseService->salvar($empresa, 'focunfe');
                Utilitarios::sendMessage("Empresa sincronizada na FocuNFe " . $empresa->razao_social);
            } catch (\Throwable $e) {
                Log::error('Erro ao sincronizar empresa com provedor NFS-e', [
                    'empresa_id' => $empresa->id,
                    'erro' => $e->getMessage(),
                ]);

                //return back()->withErrors(['provedor' => 'Não foi possível sincronizar a empresa com o provedor de NFS-e.',]);
                session()->flash('info', 'Não foi possível sincronizar a empresa com o provedor de NFS-e.');
            }
        }
        
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
        $empresaId = $id; 
        return DB::transaction(function () use ($empresaId) {

            // Verifica e bloqueia a empresa durante a operação
            $empresa = DB::table('empresas')
                ->where('id', $empresaId)
                ->lockForUpdate()
                ->first();

            if (!$empresa) {
                /*throw new \RuntimeException(
                    "Empresa {$empresaId} não encontrada."
                );*/
                session()->flash('info', "Empresa {$empresaId} não encontrada.");
                return redirect()->route('admin.empresas.index');
            }

            $database = DB::getDatabaseName();

            // Identifica todas as tabelas que possuem empresa_id
            $tabelas = DB::table('information_schema.columns')
                ->select('TABLE_NAME')
                ->where('TABLE_SCHEMA', $database)
                ->where('COLUMN_NAME', 'empresa_id')
                ->distinct()
                ->pluck('TABLE_NAME')
                ->toArray();

            // Identifica os relacionamentos entre as tabelas
            $relacionamentos = DB::table(
                'information_schema.KEY_COLUMN_USAGE'
            )
                ->select([
                    'TABLE_NAME as tabela_filha',
                    'REFERENCED_TABLE_NAME as tabela_pai',
                ])
                ->where('TABLE_SCHEMA', $database)
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->get();

            $conjunto = array_flip($tabelas);

            // Grafo: tabela filha -> tabela pai
            $grafo = [];
            $grauEntrada = [];

            foreach ($tabelas as $tabela) {
                $grafo[$tabela] = [];
                $grauEntrada[$tabela] = 0;
            }

            foreach ($relacionamentos as $relacao) {

                $filha = $relacao->tabela_filha;
                $pai = $relacao->tabela_pai;

                if (
                    isset($conjunto[$filha]) &&
                    isset($conjunto[$pai])
                ) {
                    if (!in_array($pai, $grafo[$filha])) {
                        $grafo[$filha][] = $pai;
                        $grauEntrada[$pai]++;
                    }
                }
            }

            // Ordenação topológica: filhos antes dos pais
            $fila = [];

            foreach ($grauEntrada as $tabela => $grau) {
                if ($grau === 0) {
                    $fila[] = $tabela;
                }
            }

            $ordem = [];

            while (!empty($fila)) {

                $tabela = array_shift($fila);

                $ordem[] = $tabela;

                foreach ($grafo[$tabela] as $pai) {

                    $grauEntrada[$pai]--;

                    if ($grauEntrada[$pai] === 0) {
                        $fila[] = $pai;
                    }
                }
            }

            // Impede exclusão caso existam dependências cíclicas
            if (count($ordem) !== count($tabelas)) {
                throw new \RuntimeException(
                    'Não foi possível determinar a hierarquia das tabelas. '
                    . 'Existem relacionamentos cíclicos.'
                );
            }

            $exclusoes = [];

            // Exclui os registros filhos primeiro
            foreach ($ordem as $tabela) {

                $quantidade = DB::table($tabela)
                    ->where('empresa_id', $empresaId)
                    ->delete();

                $exclusoes[$tabela] = $quantidade;
            }

            // Exclui a empresa por último
            $exclusoes['empresas'] = DB::table('empresas')
                ->where('id', $empresaId)
                ->delete();

            /*return [
                'empresa_id' => $empresaId,
                'sucesso' => true,
                'exclusoes' => $exclusoes,
                'total_registros' => array_sum($exclusoes),
            ];*/

            session()->flash('message', 'Registro Removido com Sucesso.');
            return redirect()->route('admin.empresas.index');
        });

        //session()->flash('message', 'Registro Removido com Sucesso.');

        //return redirect()->route('admin.empresas.index');
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
            ->leftJoin('notas_emitidas as e', 'e.empresa_id', '=', 'empresas.id')
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
