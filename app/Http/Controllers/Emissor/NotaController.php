<?php

namespace App\Http\Controllers\Emissor;

use App\Business\NotasBO;
use App\Http\Controllers\Controller;
use App\Models\Certificado;
use App\Models\ClassificacaoTributaria;
use App\Models\CorrelacaoTribMunTribNac;
use App\Models\CstIbsCbs;
use App\Models\Empresa;
use App\Models\EmpresaAtividade;
use App\Models\EmpresaNbs;
use App\Models\IndOpIbsCbs;
use App\Models\Municipio;
use App\Models\Nbs;
use App\Models\Tomador;
use App\Models\Uf;
use App\Traits\IssnetTrait;
use App\Utilitarios\Utilitarios;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use function PHPUnit\Framework\isNull;

class NotaController extends Controller
{
    use IssnetTrait;
    private $empresaModel;
    private $tomadorModel;
    private $estadoModel;
    private $municipioModel;
    private $nbsModel;
    private $atividadeModel;
    private $indOperModel;
    private $cstIbsCsbModel;
    private $classificacaoTributariaModel;    

    
    public function __construct(Empresa $empresaModel, Tomador $tomadorModel, 
        Uf $estadoModel, Municipio $municipioModel,
        EmpresaAtividade $atividadeModel,
        Nbs $nbsModel, IndOpIbsCbs $indOperModel, CstIbsCbs $cstIbsCsbModel, 
        ClassificacaoTributaria $classificacaoTributariaModel
    ){
        //$this->notaBO = NotasBO::newInstance();
        $this->empresaModel = $empresaModel;
        $this->tomadorModel = $tomadorModel;
        //$this->notasModel = $notasModel;
        $this->estadoModel = $estadoModel;
        $this->municipioModel = $municipioModel;
        $this->atividadeModel = $atividadeModel;
        $this->nbsModel = $nbsModel;
        $this->indOperModel = $indOperModel;
        $this->cstIbsCsbModel = $cstIbsCsbModel;
        $this->classificacaoTributariaModel = $classificacaoTributariaModel;
    }

    public function index(){
       dd('Index');
    }

    public function create(){
        $tomador = $this->tomadorModel->find(Session::get('tomador_selecionado'));
        $empresa = $this->empresaModel->with(['atividadesEmpresa'])
            ->find(Session::get('empresa_selecionada'));
        
        $estados = $this->estadoModel->getListaEstados();
        $uf_id = $empresa->cidade()->first()->uf_id;
        $cidades = $this->municipioModel->municipios($uf_id);

        $dadosCadastrais = json_decode($empresa->dados_cadastrais);

        $verificar_validade_atividade = $empresa->atividadesEmpresa()
            ->where('id', $empresa->empresa_atividade_id)
            ->where(function ($query) {
                $query->whereNull('vigencia_final')
                    ->orWhere('vigencia_final', '>=', now());
            })
            ->first();
        
        if(!is_null($verificar_validade_atividade)){
            if (!isNull($verificar_validade_atividade->vigencia_final) && Carbon::parse($verificar_validade_atividade->vigencia_final)->isPast()) {
                session()->flash('danger', 'Opss! A vigência da Atividade no Municipio('. $verificar_validade_atividade->descricao_atividade .') selecionada está Expirada!');
                return redirect()->route('empresas.edit', $empresa->id);
            }
        }

        $certificadoCliente = Certificado::where('empresa_id', $empresa->id)
            ->first();

        if(is_null($certificadoCliente)){
            session()->flash('danger', 'Opss! A empresa não possui um Certificado Digital válido cadastrado.');
            return redirect()->route('empresas.edit', $empresa->id);
        }
    
        $atividades = $this->atividadeModel->atividadesByCTribMunList($empresa->id);
        $atividade = $this->atividadeModel
            ->where('empresa_id', $empresa->id)
            ->where('id', $empresa->empresa_atividade_id)->first();

        $data_competencia = date('Y-m-d');

        $cod_trib_nac = [null => 'Selecione o Código de Tributação Nacional'] + CorrelacaoTribMunTribNac::select(
                'cTribNac',
                DB::raw("concat(cTribNac, ' - ', IFNULL(xTribNac, '')) as field1")
            )
            ->where('cTribMun', $atividade->codigo_atividade)
            ->where('empresa_id', $empresa->id)
            ->orderBy('cTribMun', 'asc')
            ->pluck('field1', 'cTribNac')
            ->all();

        $situacao_simples_nacional = Empresa::getOpcaoSimplesNacional();//Regime de Apuração Tributária pelo Simples Nacional, campo regApTribSN em regTrib
        //Regime de Apuração Tributária pelo Simples Nacional.
        $regimes_apuracao_sn = Empresa::getRegimeApuracaoSimplesNacional();
        //Tipos de Regimes Especiais de Tributação Municipal:
        
        $situacao_simples_nacional = array_filter($situacao_simples_nacional, function($chave) use ($empresa) {
            return (int)$chave === (int)$empresa->op_simp_nac;
        }, ARRAY_FILTER_USE_KEY);
        
        $regimes_apuracao_sn = array_filter($regimes_apuracao_sn, function($chave) use ($empresa) {
            return (int)$chave === (int)$empresa->tp_reg_apuracao_sn;
        }, ARRAY_FILTER_USE_KEY);

        ///Campo ddlTribISSQN
        $dadosCadastrais = json_decode($empresa->dados_cadastrais, true);
        
        $tributacaoIssqnList = [
            null => 'Selecione',
            1 => 'Operação Tributável',
		    2 => 'Imunidade',
			3 => 'Exportação de serviço',
			4 => 'Não Incidência',
        ];

        if(isset($dadosCadastrais['tributacoesPermitidas']['tribISSQN']) && $dadosCadastrais['tributacoesPermitidas']['tribISSQN'] == 1){
            unset($tributacaoIssqnList[2]);
            unset($tributacaoIssqnList[3]);
            unset($tributacaoIssqnList[4]);
        }else{
            unset($tributacaoIssqnList[2]);
            unset($tributacaoIssqnList[3]);
            unset($tributacaoIssqnList[4]);
        }

        $tiposImunidadeList = [
            null => 'Selecione',
            0 => 'Imunidade',
            1 => 'Patrimônio, renda ou serviços, uns dos outros (CF88, Art 150, VI, a)',
            2 => 'Templos de qualquer culto (CF88, Art 150, VI, b)',
            3 => 'Patrimônio, renda ou serviços dos partidos políticos, inclusive suas fundações, das entidades sindicais dos trabalhadores, das instituições de educação e de assistência social, sem fins lucrativos, atendidos os requisitos da lei (CF88, Art 150, VI, c)',
            4 => 'Livros, jornais, periódicos e o papel destinado a sua impressão (CF88, Art 150, VI, d)',
            5 => 'Fonogramas e videofonogramas musicais produzidos no Brasil contendo obras musicais ou literomusicais de autores brasileiros e/ou obras em geral interpretadas por artistas brasileiros bem como os suportes materiais ou arquivos digitais que os contenham, salvo na etapa de replicação industrial de mídias ópticas de leitura a laser. (CF88, Art 150, VI, e)',
        ];

        $tiposSuspencaoExigibilidade = [
            null => 'Selecione',
            1 => 'Exigibilidade Suspensa por Decisão Judicial',
			2 => 'Exigibilidade Suspensa por Processo Administrativo'
        ];

        $tipos_regime_esp_trib_mun = Empresa::getTiposRegimeEspecialTributacaoMunicipio();
        $tipos_regime_esp_trib_mun = array_filter($tipos_regime_esp_trib_mun, function($chave) use ($empresa) {
            return (int)$chave === (int)$empresa->tp_regime_esp_trib_mun;
        }, ARRAY_FILTER_USE_KEY);
        $tipos_regime_esp_trib_mun = ['' => 'Selecione'] + $tipos_regime_esp_trib_mun;

        $tipos_retencoes = [
            1 => 'Não retido',
            2 => 'Retido pelo Tomador',
            3 => 'Retido pelo Intermediário'
        ];

        //$municipio_incidencia = $empresa->cidade_id;
        $municipio_incidencia = Municipio::where('codigo', $empresa->cidade_id)->first();

        //indicador de operação
        $indOpIbsCbs = $this->indOperModel->indicadorOperacoes();
        $cstIbsCsb = $this->cstIbsCsbModel->listar();

        return view('emissor.create', [
            'data_competencia' => $data_competencia,
            'tomador' => $tomador,
            'empresa' => $empresa,
            'estados' => $estados,
            'uf_id' => $uf_id,
            'cidades' => $cidades,
            'dados_cadastrais' => $dadosCadastrais,
            'atividades' => $atividades,
            'cod_trib_nac' => $cod_trib_nac,
            'atividade' => $atividade,
            'situacao_simples_nacional' => $situacao_simples_nacional,
            'regimes_apuracao_sn' => $regimes_apuracao_sn,
            'tributacao_issqn_list' => $tributacaoIssqnList,
            'tiposImunidadeList' => $tiposImunidadeList,
            'tiposSuspencaoExigibilidade' => $tiposSuspencaoExigibilidade,
            'tipos_regime_esp_trib_mun' => $tipos_regime_esp_trib_mun,
            'tipos_retencoes' => $tipos_retencoes,
            'municipio_incidencia' => $municipio_incidencia,
            'indOpIbsCbs' => $indOpIbsCbs,
            'cstIbsCsb' => $cstIbsCsb,
        ]);
    }

    public function store(Request $request){
        try{
            $empresaSessao = request()->session()->get('empresa_selecionada');
            $empresaSessao = $this->empresaModel->find($empresaSessao);
            
            $dados = $request->all();
            //$dados = $this->notaBO->tratarDados($dados);

            
            dd($dados);
        }catch(\Exception $e){
            DB::insert(
                'INSERT INTO internal_logs (empresa_id, description) VALUES (?, ?)',
                [
                    $empresaSessao->id,
                    $e->getMessage()
                ]
            );
            session()->flash('danger', 'Opss! Houve falha na Emissão da NFS-e');
        }

        return redirect()->route('nota.index');
    }

    /* Pesquisas */
    public function obterTributacaoNacionalPorAtividadeMun(Request $request)
    {                
        $dados = CorrelacaoTribMunTribNac::query()->
        select(
                'cTribNac',
                DB::raw("concat(cTribNac, ' - ', IFNULL(xTribNac, '')) as descricao")
            )
            ->where('cTribMun', $request->cTribMun)
            ->where('empresa_id', Session::get('empresa_selecionada'))
            ->orderBy('cTribMun', 'asc')
            ->get();
        
        return response()->json($dados,200,[],JSON_UNESCAPED_UNICODE);
    }  

    public function obterNbs(Request $request){
        $corrTrib = CorrelacaoTribMunTribNac::where('cTribNac', $request->cTribNac)
            ->where('empresa_id', Session::get('empresa_selecionada'))
            ->first();

        $dados = EmpresaNbs::query()->
        select(
                'codigo',
                DB::raw("concat(codigo, ' - ', IFNULL(descricao, '')) as descricao")
            )
            ->where('correlaca_trib_id', $corrTrib->id)
            ->where('empresa_id', Session::get('empresa_selecionada'))
            ->orderBy('codigo', 'asc')
            ->get();
        
        return response()->json($dados,200,[],JSON_UNESCAPED_UNICODE);
    }

    public function obterPercentualAtividadeMunicipio(){
        $identificador = request()->q;
        $atividade = EmpresaAtividade::where('empresa_id', Session::get('empresa_selecionada'))
            ->where('codigo_atividade', $identificador)
            ->first();

        return response()->json($atividade,200,[],JSON_UNESCAPED_UNICODE);
    }

    public function obterPercentualTribNac(){
        $identificador = request()->q;

        $corrTrib = CorrelacaoTribMunTribNac::where('cTribNac', $identificador)
            ->where('empresa_id', Session::get('empresa_selecionada'))
            ->first();
        
        return response()->json($corrTrib,200,[],JSON_UNESCAPED_UNICODE);
    }

    public function obterClassificacoesTributarias(){
        $identificador = request()->q;

        $classificacoes = CstIbsCbs::where('codigo', $identificador)
            ->first()
            ->classificacoesTributarias()
            ->ativos()
            ->orderBy('codigo')
            ->get();

        return response()->json($classificacoes,200,[],JSON_UNESCAPED_UNICODE);
    }
}
