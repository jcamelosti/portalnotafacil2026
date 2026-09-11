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
use App\Models\Temp;
use App\Models\Tomador;
use App\Models\Uf;
use App\Traits\IssnetTrait;
use App\Utilitarios\Utilitarios;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;
use JCamelo\NfseNacionalLib\DTO\DPSDataSnDTO;
use JCamelo\NfseNacionalLib\Manager\CertificateManager;
use JCamelo\NfseNacionalLib\Security\DPSXmlSigner;
//use JCamelo\NfseNacionalLib\Factories\DPSFactory;

use JCamelo\NfseNacionalLib\Services\NFSeService;
use JCamelo\NfseNacionalLib\XML\Builders\DPSSnXmlBuilder;
use ZipStream\Test\Util;

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
    private $tempModel;  
    private $notaBO;

    private NFSeService $nfse;
    
    public function __construct(Empresa $empresaModel, Tomador $tomadorModel, 
        Uf $estadoModel, Municipio $municipioModel,
        EmpresaAtividade $atividadeModel,
        Nbs $nbsModel, IndOpIbsCbs $indOperModel, CstIbsCbs $cstIbsCsbModel, 
        ClassificacaoTributaria $classificacaoTributariaModel, Temp $tempModel,
        NFSeService $nfse, 
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
        $this->tempModel = $tempModel;

        $this->notaBO = NotasBO::newInstance();

        $this->nfse = $nfse;
    }

    private static function numero(mixed $valor): string
    {
        if ($valor === null || $valor === '') {
            return '0.00';
        }

        if (is_string($valor)) {
            $valor = trim($valor);

            // Formato brasileiro: 1.500,99
            if (str_contains($valor, ',')) {
                $valor = str_replace('.', '', $valor);
                $valor = str_replace(',', '.', $valor);
            }
        }

        return number_format((float) $valor, 2, '.', '');
    }

    public function definirMunicipioIncidencia(
        string $cTribNac,
        string $tributacaoIssqn,
        bool $exigibilidadeSuspensa,
        bool $regimeEspecial,
        string $municipioPrestador,
        string $municipioTomador,
        string $municipioPrestacao,
        string $localPrestacao
    ): ?string {
        // Não informar cLocIncid
        if (
            in_array($tributacaoIssqn, [2, 3,4])
            || $exigibilidadeSuspensa
            || $regimeEspecial
        ) {
            return null;
        }

        // Águas Marítimas
        if (
            $cTribNac !== '200101'
            && $localPrestacao === 'AGUAS_MARITIMAS'
        ) {
            return $municipioPrestador;
        }

        // Serviços cujo município é o local da prestação
        $codigosLocalPrestacao = [
            '030401',
            '030402',
            '030403',
            '030501',
            '070201',
            '070202',
            '070401',
            '070501',
            '070502',
            '070901',
            '070902',
            '071001',
            '071002',
            '071101',
            '071102',
            '071201',
            '071601',
            '071701',
            '071801',
            '071901',
            '110101',
            '110102',
            '110201',
            '110401',
            '110402',
            '120101',
            '120201',
            '120301',
            '120401',
            '120501',
            '120601',
            '120701',
            '120801',
            '120901',
            '120902',
            '120903',
            '121001',
            '121101',
            '121201',
            '121401',
            '121501',
            '121601',
            '121701',
            '141401',
            '141402',
            '141403',
            '141404',
            '160101',
            '160102',
            '160103',
            '160104',
            '160201',
            '171001',
            '171002',
            '200101',
            '200102',
            '200201',
            '200301',
            '220101',
        ];

        if (in_array($cTribNac, $codigosLocalPrestacao)) {
            return $municipioPrestacao;
        }

        // Código 170501
        if ($cTribNac === '170501') {
            return $municipioTomador;
        }

        // Demais códigos
        return $municipioPrestador;
    }

    public function index(){
        /*$temp = Temp::all();
        $dados = $temp[count($temp) - 1]->dados;
        $this->emitir($dados); */

        dd("Index");
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

        $dados_cadastrais = json_decode($empresa->dados_cadastrais, true);
 
        return view('emissor.create', [
            'dados_cadastrais' => $dados_cadastrais,
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

    private function emitir($dados){
        $dados = $this->notaBO->tratarDados($dados);

        $empresa = $this->empresaModel->with(['atividadesEmpresa'])
            ->find(Session::get('empresa_selecionada'));

        $tomador = $this->tomadorModel->find($dados['tomador_id']);

        $localPrestacao = $this->definirMunicipioIncidencia(
            $dados['cTribNac'],
            $dados['ddlTribISSQN'],
            0,//bool $exigibilidadeSuspensa,
            0,//bool $regimeEspecial,
            $empresa->cidade()->first()->codigo,
            $tomador->cidade()->first()->codigo,
            $dados['ddlCidadePrestacao'],
            $dados['ddlCidadePrestacao']
        );
        
        /*$dataSN = new DPSDataDTO(
            ambiente: $empresa->ambiente_emissao == 'HOMOLOGACAO' ? 2 : 1,
            dataEmissao: Carbon::now()->format('Y-m-d\TH:i:sP'),
            serieDps: $empresa->serie_dps,
            numDps: ($empresa->num_ultimo_dps + 1),

            cnpjPrestador: $empresa->cpf_cnpj,
            imPrestador: $empresa->inscricao_municipal,
            
            razaoTomador: $empresa->razao_social,
            cnpjTomador: $tomador->cpf_cnpj,     
            
            cMunTomador: $tomador->cidade()->first()->codigo,
            cepTomador: preg_replace('/[^\d\-]/', '', $tomador->cep),
            logradouroTomador: $tomador->logradouro,
            numeroTomador: $tomador->numero,
            complementoTomador: $tomador->complemento,
            bairroTomador: $tomador->bairro,
            cPaisTomadorExterior: '',
            cEndPostTomador: '',
            xCidadeTomador: '',      
            
            localPrestacaoServico: $localPrestacao,

            codigoMunicipio: $dados['ddlCidadePrestacao'], //municipio do prestado - cLocEmi
            codigoTributacaoNacional: $dados['cTribNac'],
            codigoServico: $dados['empresa_atividade_id'],
            descricaoServico: $dados['txtDescServicos'],
            valorServico: $this->numero($dados['txtTotal']),
            dataCompetencia: date('Y-m-d'),
            nbs: $dados['nbs'],
            complemento: $dados['txtInfoComplementares'],

            opSimpNac: $empresa->op_simp_nac,
            regApTribSN: $empresa->tp_reg_apuracao_sn,//só quando for do simples
            regEspTrib: $empresa->tp_regime_esp_trib_mun,
            tribISSQN: $dados['ddlTribISSQN'],
            tpRetISSQN: $dados['ddlTipoRetencao'],
            tribMunAliq: (float)$this->numero($dados['txtAliquota']),
            
            tribFedCst: $dados['ddlSitTribFederal'],
            tpRetPisCofins: $dados['ddlTipoRetFederal'],
            vRetCP: $this->numero($dados['txtValorCP']),
            vRetIRRF: $this->numero($dados['txtValorIRRF']),
            vRetCSLL: isset($dados['txtValorCSLL']) ? $this->numero($dados['txtValorCSLL']) : 0.00,

            pTotTribSN: $this->numero($dados['txtPercentualTribSN']),
            cIndOp: $dados['ddlIndicadorOperacao'],
            cstIbsCbs: $dados['ddlSituacaoTributaria'],
            cClassTrib: $dados['ddlClassificacaoTributaria'],
            //indDest: 0,
        );*/

        $totalNfse = (float)$dados['txtTotal'];

        //Calculos PIs e Cofins
        if(isset($dados['txtBaseCalcFederal']) && !empty($dados['txtBaseCalcFederal'])){
            $base = (float) $dados['txtBaseCalcFederal'];
            $aliqPis = (float) $dados['txtAliqPIS'];
            $aliqCofins = (float) $dados['txtAliqCOFINS'];
            $valorPis = round($base * ($aliqPis / 100), 2);
            $valorCofins = round($base * ($aliqCofins / 100), 2);
            $resultadoCalcPisCofins = [
                'baseCalculoFederal' => number_format($base, 2, '.', ''),
                'aliqPis' => number_format($aliqPis, 2, '.', ''),
                'aliqCofins' => number_format($aliqCofins, 2, '.', ''),
                'valorPis' => number_format($valorPis, 2, '.', ''),
                'valorCofins' => number_format($valorCofins, 2, '.', ''),
            ];
        }else{
            $resultadoCalcPisCofins = [
                'baseCalculoFederal' => null,
                'aliqPis' => null,
                'aliqCofins' => null,
                'valorPis' => null,
                'valorCofins' => null,
            ];
        }       

        //correção 08/09/2026
        $dataSN = new DPSDataSnDTO(
            ambiente: $empresa->ambiente_emissao == 'HOMOLOGACAO' ? 2 : 1,
            dataEmissao: Carbon::now('America/Sao_Paulo')->format('Y-m-d\TH:i:sP'),
            serie: $empresa->serie_dps,
            numDps: ($empresa->num_ultimo_dps + 1),
            dataCompetencia: Carbon::now(
                'America/Sao_Paulo'
            )->format('Y-m-d'),
            codigoMunicipio: $dados['ddlCidadePrestacao'],
            //prestador
            cnpjPrestador: $empresa->cpf_cnpj,
            imPrestador: $empresa->inscricao_municipal,
            fonePrestador: preg_replace('/[^0-9]/', '', $empresa->telefone1) ?? null,
            emailPrestador: $empresa->email ?? null,

            //Regime da Empresa
            opSimpNac: $empresa->op_simp_nac,
            regApTribSN: $empresa->tp_reg_apuracao_sn,//só quando for do simples
            regEspTrib: $empresa->tp_regime_esp_trib_mun,

            //dados tomador - quando não for no exterior
            cnpjTomador: strlen($tomador->cpf_cnpj) == 14 ? $tomador->cpf_cnpj : null,
            cpfTomador: strlen($tomador->cpf_cnpj) < 14 ? $tomador->cpf_cnpj : null,
            razaoTomador: $tomador->razao_social,
            codigoMunicipioTomador: $tomador->cidade()->first()->codigo,
            cepTomador: preg_replace('/[^0-9]/', '', $tomador->cep) ?? null,
            logradouroTomador: $tomador->logradouro,
            numeroTomador: $tomador->numero ?? null,
            complementoTomador: $tomador->complemento ?? null,
            bairroTomador: $tomador->bairro ?? null,
            foneTomador: preg_replace('/[^0-9]/', '', $empresa->telefone1) ?? null,
            emailTomador: $tomador->email ?? null,
            
            //dados sobre o serviço
            codigoTributacaoNacional: $dados['cTribNac'],
            codigoServicoMunicipal: $dados['empresa_atividade_id'],
            descricaoServico: $dados['txtDescServicos'],
            codigoNbs: $dados['nbs'],
            codigoMunicipioPrestacao: $localPrestacao, //Local da Prestação de Serviço
            valorServico: number_format($totalNfse, 2, '.', ''),
                        
            //issqn
            tributaIss: $dados['ddlTribISSQN'],
            tipoRetencaoIss: $dados['ddlTipoRetencao'],
            aliquotaIss: $dados['txtAliquota'],//string '2.5'
            
            cstPisCofins: $dados['ddlSitTribFederal'],
            //vBCPisCofins
            baseCalculoPisCofins: $resultadoCalcPisCofins['baseCalculoFederal'],
            //pAliqPis
            aliquotaPis: $resultadoCalcPisCofins['aliqPis'],
            //pAliqCofins
            aliquotaCofins: $resultadoCalcPisCofins['aliqCofins'],
            //vPis
            valorPis: $resultadoCalcPisCofins['valorPis'],
            //vCofins
            valorCofins: $resultadoCalcPisCofins['valorCofins'],
            tipoRetencaoPisCofins: $dados['ddlTipoRetFederal'],

            //Cp, Irrf, Csll
            valorRetencaoCp: number_format($dados['txtValorCP'] ?? null, 2, '.', ''),
            valorRetencaoIrrf: number_format($dados['txtValorIRRF'] ?? null, 2, '.', ''),
            valorRetencaoCsll: number_format($dados['txtValorCSLL'] ?? null, 2, '.', ''),
            percentualTotalTributos: number_format($dados['txtPercentualTribSN'], 2, '.', ''),

            finNfse: 0,
            cIndOp: $dados['ddlIndicadorOperacao'],
            indDest: 0,
            cstIbsCbs: $dados['ddlSituacaoTributaria'],
            cClassTrib: $dados['ddlClassificacaoTributaria'],
            informacaoComplementar: $dados['txtInfoComplementares'] ?? null,
        );
        
        //Gerar NFSe
        $retorno = $this->nfse->gerarNfse('issnet', $dataSN, $empresa->id);
        
        if(isset($retorno->sBody->GerarNfseResponse->GerarNfseResposta->ListaMensagemRetorno->MensagemRetorno)){
             echo "Falha";
            dd($retorno->sBody->GerarNfseResponse->GerarNfseResposta->ListaMensagemRetorno->MensagemRetorno);
        }else{
            dd($retorno);
        }        

        /*$nfse = $retorno['ListaNfse']['CompNfse']['Nfse']['infNFSe'];
        $protocolo = $retorno['Protocolo'];
        $data_recebimento = date('Y-m-d', strtotime((string)  $retorno['DataRecebimento']));//data recebimento lote
        $valorServico = $nfse['DPS']['infDPS']['valores']['vServPrest']['vServ'];*/
    }

    public function store(Request $request){
        try{
            $empresaSessao = request()->session()->get('empresa_selecionada');
            $empresaSessao = $this->empresaModel->find($empresaSessao);
            
            $dados = $request->all();
            //$dados = $this->notaBO->tratarDados($dados);
            $dados = $request->except('_token');

            /*$temp = Temp::create([
                'dados' => $dados,
            ]);*/
            $this->emitir($dados);
        }catch(\Exception $e){
            dd($e->getMessage());
            /*DB::insert(
                'INSERT INTO internal_logs (empresa_id, description) VALUES (?, ?)',
                [
                    $empresaSessao->id,
                    $e->getMessage()
                ]
            );
            session()->flash('danger', 'Opss! Houve falha na Emissão da NFS-e');*/
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
