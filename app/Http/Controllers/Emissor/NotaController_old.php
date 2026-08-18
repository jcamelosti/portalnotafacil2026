<?php

namespace App\Http\Controllers\Emissor;

use App\Business\NotasBO;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotaCreateRequest;
use App\Models\Certificado;
use App\Models\CnaeLc;
use App\Models\Empresa;
use App\Models\EmpresaAtividade;
use App\Models\EmpresaCnae;
use App\Models\ListaServico;
use App\Models\Municipio;
use App\Models\Nbs;
use App\Models\Nfse as ModelsNfse;
use App\Models\NotaEmitida;
use App\Models\Protocolo;
use App\Models\Tomador;
use App\Models\Uf;
use App\Traits\IssnetTrait;
use App\Utilitarios\Utilitarios;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use NFePHP\NFSe\Models\Issnet\RpsClass;

use function PHPUnit\Framework\isNull;

class NotaController extends Controller
{
    use IssnetTrait;

    private $notaBO;
    private $empresaModel;
    private $tomadorModel;
    private $notasModel;
    private $estadoModel;
    private $municipioModel;
    private $nfseModel;
    private $cnaeModel;
    private $atividadeModel;
    private $listaServicoModel;
    private $nbsModel;
    
    public function __construct(Empresa $empresaModel, Tomador $tomadorModel, 
        ModelsNfse $notasModel, Uf $estadoModel, Municipio $municipioModel,
        NotaEmitida $nfseModel, EmpresaCnae $cnaeModel, EmpresaAtividade $atividadeModel,
        ListaServico $listaServicoModel, Nbs $nbsModel
    ){
        $this->notaBO = NotasBO::newInstance();
        $this->empresaModel = $empresaModel;
        $this->tomadorModel = $tomadorModel;
        $this->notasModel = $notasModel;
        $this->estadoModel = $estadoModel;
        $this->municipioModel = $municipioModel;
        $this->nfseModel = $nfseModel;
        $this->cnaeModel = $cnaeModel;
        $this->atividadeModel = $atividadeModel;
        $this->listaServicoModel = $listaServicoModel;
        $this->nbsModel = $nbsModel;
    }

    public function index(){
        $campos = request()->all();

        $empresaSelecionada = Session::get('empresa');
        $data = [
            'data_inicio' => request()->data_inicio ?? Carbon::today()->subDays(30)->format('Y-m-d'),
            'data_fim'    => request()->data_fim ?? Carbon::today()->format('Y-m-d'),
        ];

        // converter para Carbon
        $inicio = Carbon::parse($data['data_inicio']);
        $fim    = Carbon::parse($data['data_fim']);

        // validação
        if ($inicio->diffInDays($fim) > 30) {
            session()->flash('danger', 'Opss! O período máximo permitido é de 30 dias.');
            return redirect()->route('nota.index');
        }

        $notas = $this->nfseModel
            ->where('empresa_id', Session::get('empresa_selecionada'))
            ->when(!empty($data['data_inicio']) && !empty($data['data_fim']), function ($query) use ($data) {
                $query->whereBetween('created_at', [
                    $data['data_inicio'].' 00:00:00',
                    $data['data_fim'].' 23:59:59'
                ]);
            })
            ->where(function($query) use($campos) {
                if(isset($campos['tomador_id']) && $campos['tomador_id'] != '0'){
                    $query->where('tomador_id', $campos['tomador_id']);
                }
            })
            ->orderBy('num_nfse', 'desc')
            ->paginate(10);

        
        $tomadoresList = $this->tomadorModel
        ->tomadoresList(Session::get('empresa_selecionada'));
        
        return view('emissor.listagem_notas', [
            'notas' => $notas, 
            'empresa' => $empresaSelecionada,
            'data' => $data,
            'tomadoresList' => $tomadoresList
        ]);
    }

    public function create(){
        $tomador = $this->tomadorModel->find(Session::get('tomador_selecionado'));
        $empresa = $this->empresaModel->with(['cnaes', 'atividadesEmpresa'])
            ->find(Session::get('empresa_selecionada'));  
            
        $isMei = $empresa->is_mei ? 'display: none;' : '';
        
        $permiteDescontoCond = $empresa->permite_desc_cond == 2 ? 'display: none;' : '';
        $permiteDescontoInc = $empresa->permite_desc_incond == 2 ? 'display: none;' : '';
        $permiteDeducao = $empresa->permite_deducao == 2 ? 'display: none;' : '';

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
       
        /*$aliquotaAtividade = number_format($empresa->atividadesEmpresa()
            ->where('id', $empresa->empresa_atividade_id)
            ->first()
            ->aliquota, 2, ',', '');*/
        $aliquotaAtividade = null;
        
        //if(isset($dadosCadastrais->PermiteTributarFora) && $dadosCadastrais->PermiteTributarFora == 1){
            $listTributacao = [
                1 => 'Tributação no município',
                2 => 'Tributação fora do município'
            ];
        /*}else{
            $listTributacao = [
                1 => 'Tributação no município',
            ];
        }*/

        //cnaes
        $cnaes = $this->cnaeModel->cnaesList($empresa->id);
        //atividades
        $atividades = $this->atividadeModel->atividadesList($empresa->id);

        //filtro items lc conforme cnae
        $listaCnae = $this->cnaeModel->find($empresa->empresa_cnae_id);
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

        $data_competencia = date('Y-m-d');
        
        return view('emissor.create', [
            'data_competencia' => $data_competencia,
            'tomador' => $tomador,
            'empresa' => $empresa,
            'isMei' => $isMei,
            'permiteDescontoInc' => $permiteDescontoInc,
            'permiteDescontoCond' => $permiteDescontoCond,
            'permiteDeducao' => $permiteDeducao,
            'estados' => $estados,
            'uf_id' => $uf_id,
            'cidades' => $cidades,
            'dados_cadastrais' => $dadosCadastrais,
            'listTributacao' => $listTributacao,
            'aliquotaAtividade' => $aliquotaAtividade,
            'cnaes' => $cnaes,
            'atividades' => $atividades,
            'servicos' => $servicos,
            'nbs_list' => $nbs_list
        ]);
    }

    public function substituirNota($id){
        $nfse = $this->nfseModel->find($id);
        $tomador = $this->tomadorModel->find($nfse->tomador_id);
        $empresa = $this->empresaModel->with(['cnaes', 'atividadesEmpresa'])
            ->find(Session::get('empresa_selecionada'));

        $isMei = $empresa->is_mei ? 'display: none;' : '';
        
        $permiteDescontoCond = $empresa->permite_desc_cond == 2 ? 'display: none;' : '';
        $permiteDescontoInc = $empresa->permite_desc_incond == 2 ? 'display: none;' : '';
        $permiteDeducao = $empresa->permite_deducao == 2 ? 'display: none;' : '';

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

        if (!isNull($verificar_validade_atividade->vigencia_final) && Carbon::parse($verificar_validade_atividade->vigencia_final)->isPast()) {
            session()->flash('danger', 'Opss! A vigência da Atividade no Municipio('. $verificar_validade_atividade->descricao_atividade .') selecionada está Expirada!');
            return redirect()->route('empresas.edit', $empresa->id);
        }
       
        /*$aliquotaAtividade = number_format($empresa->atividadesEmpresa()
            ->where('id', $empresa->empresa_atividade_id)
            ->first()
            ->aliquota, 2, ',', '');*/
        $aliquotaAtividade = null;
        
        //if(isset($dadosCadastrais->PermiteTributarFora) && $dadosCadastrais->PermiteTributarFora == 1){
            $listTributacao = [
                1 => 'Tributação no município',
                2 => 'Tributação fora do município'
            ];
        /*}else{
            $listTributacao = [
                1 => 'Tributação no município',
            ];
        }*/
        
        //dd($nfse->num_nfse . ' ' . $nfse->serie_rps);
        $motivo = [
            '' => 'Selecione o Motivo',
            '1' =>  'Erro na emissão',
			'2' =>  'Serviço não prestado',
			//'3' =>  'Erro de assinatura', //uso da prefeitura
			'4' =>  'Duplicidade da nota',
			//'5' =>  'Erro de processamento', //uso da prefeitura
        ];

        //cnaes
        $cnaes = $this->cnaeModel->cnaesList($empresa->id);
        //atividades
        $atividades = $this->atividadeModel->atividadesList($empresa->id);

        //filtro items lc conforme cnae
        $listaCnae = $this->cnaeModel->find($empresa->empresa_cnae_id);
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

        $data_competencia = date('Y-m-d');


        //preenchendo os dados
         $dados_nota_original = [];
        if(!empty($nfse->dados_emissao_json)){
            $dados_nota_original = $nfse->dados_emissao_json;
            $uf_id = $dados_nota_original['uf'];
            $empresa->cidade_id = $dados_nota_original['cidade_id'];                
            $aliquotaAtividade = $dados_nota_original['txtAliq'] ?? $aliquotaAtividade;

            $nbs_list = $this->nbsModel->getListaNbs($dados_nota_original['item_lc_id']);
            
            $camposFormatar = [
                "txtAliq",
                "txtDescontoCondicionado",
                "txtDescontoInCondicionado",
                "txtDeducaoBaseCalculo",
                "txtTotalISSQN",
                "txtPis",
                "txtCofins",
                "txtOutros",
                "txtIRRF",
                "txtCSLL",
                "txtISSQNResponsavel",
                "txtOutrasRetencoes",
                "txtValorDescontos",
                "txtTotalRetencoes",
                "txtTotalValorLiquido",
            ];

            
            foreach ($camposFormatar as $campo) {
                if (isset($dados_nota_original[$campo])) {

                    // Normaliza para float (seguro para string ou número)
                    $valor = $dados_nota_original[$campo];
                    //$valor = str_replace('.', ',', $valor);
                    //$valor = str_replace(',', '.', $valor);
                    $numero = (float) $valor;

                    // Aplica regra
                    $dados_nota_original[$campo] = $numero > 0
                        ? number_format($numero, 2, ',', '.')
                        : null;
                }
            }
        }

        return view('emissor.create', [
            'data_competencia' => $data_competencia,
            'tomador' => $tomador,
            'empresa' => $empresa,
            'isMei' => $isMei,
            'permiteDescontoInc' => $permiteDescontoInc,
            'permiteDescontoCond' => $permiteDescontoCond,
            'permiteDeducao' => $permiteDeducao,
            'estados' => $estados,
            'uf_id' => $uf_id,
            'cidades' => $cidades,
            'dados_cadastrais' => $dadosCadastrais,
            'listTributacao' => $listTributacao,
            'aliquotaAtividade' => $aliquotaAtividade,
            'nfse' => $nfse,
            'motivo' => $motivo,
            'servicos' => $servicos,
            'cnaes' => $cnaes,
            'atividades' => $atividades,
            'nbs_list' => $nbs_list,
            'nota_original' => $dados_nota_original
        ]);
    }

    public function store_substituicao(Request $request){
       try{
            $empresaSessao = request()->session()->get('empresa_selecionada');
            $empresaSessao = $this->empresaModel->find($empresaSessao);
            
            $dados = $request->all();
            $dados = $this->notaBO->tratarDados($dados);

            $retorno = $this->emitir_substituicao($dados);

            if($retorno['error']){
                session()->flash('danger', $retorno['message']);
                return redirect()->back()->withInput($dados);
            }

            //gravando Protocolo para consultar depois
            //numero do rps
            $numeroRps = $retorno['data']['numero_rps']; 
            //numero da nota
            $numeroNota = $retorno['data']['numero_nota']; 

            //atualizando dados da ultima nota emitida
            $empresaSessao->num_ultima_nota = $numeroRps;
            $empresaSessao->save();

            //salvando nota temporaria
            $novaNota = [
                'empresa_id' => $empresaSessao->id,
                'tomador_id' => $dados['tomador_id'],
                'num_nfse' => $numeroNota,
                'cod_verificacao_nfse' => '******',
                'data_emissao_nfse' => date('Y-m-d'),
                'numero_rps' => $numeroRps,
                'serie_rps' => $retorno['data']['serie'],
                'tipo_rps' => $retorno['data']['tipo'],
                'competencia' => date('Ym'),
                'data_emissao_rps' => date('Y-m-d'),
                'valor_nota' => $dados['txtTotalValorLiquido'],
                'cod_trib_mun' => 0,
            ];
            
            $notaFiscalServico = new NotaEmitida();
            $nota = $notaFiscalServico->adicionarNfse($novaNota);
            $urlNota = $this->consultarUrlNota($nota, null);
            $nota->url_view = $urlNota;
            $nota->save();
            
            Utilitarios::sendMessage('Nota de Substituição Emitida por: '. $empresaSessao->razao_social.'| Nota Número: ' . $numeroRps);
        }catch(\Exception $e){
            $detalhesErro = sprintf(
                "Erro Emissão NFS-e: %s | Código: %s | Arquivo: %s | Linha: %s\nTrace: %s",
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            );

            Log::info($detalhesErro);

            session()->flash('danger', 'Opss! Houve falha na Emissão da NFS-e');
            //Utilitarios::sendMessage('Erro Emissão NFS-e #' . $detalhesErro);            
        }

        //return redirect()->route('nota.index');
        return redirect()->route('sincnotasempresa', 0);
    }

    public function store(NotaCreateRequest $request){
        try{
            $empresaSessao = request()->session()->get('empresa_selecionada');
            $empresaSessao = $this->empresaModel->find($empresaSessao);
            
            $dados = $request->all();
            $dados = $this->notaBO->tratarDados($dados);
            
            $retorno = $this->emitir($dados);
        
            if($retorno['error']){
                session()->flash('danger', $retorno['message']);
                return redirect()->back()->withInput($dados);
            }

            //gravando Protocolo para consultar depois
            //numero do rps
            $numeroRps = $retorno['data']['numero_rps']; 
            //numero da nota
            $numeroNota = $retorno['data']['numero_nota']; 

            //atualizando dados da ultima nota emitida
            $empresaSessao->num_ultima_nota = $numeroRps;
            $empresaSessao->save();

            /*Protocolo::create([
                'empresa_id' => $empresaSessao->id,
                'tomador_id' => $dados['tomador_id'],
                'num_nfse' => $numeroNota,
                'protocolo' => $protocoloGerado,
                'descricao_servico' => $dados['txtDescServicos'],
                'valor_liquido' => $dados['txtTotalValorLiquido'],
                'valor_total' => $dados['txtTotal']
            ]);*/

            //salvando nota temporaria
            $novaNota = [
                'empresa_id' => $empresaSessao->id,
                'tomador_id' => $dados['tomador_id'],
                'num_nfse' => $numeroNota,
                'cod_verificacao_nfse' => '******',
                'data_emissao_nfse' => date('Y-m-d'),
                'numero_rps' => $numeroRps,
                'serie_rps' => $retorno['data']['serie'],
                'tipo_rps' => $retorno['data']['tipo'],
                'competencia' => date('Ym'),
                'data_emissao_rps' => date('Y-m-d'),
                'valor_nota' => $dados['txtTotalValorLiquido'],
                'cod_trib_mun' => 0,
                'dados_emissao_json' => $dados
            ];
            
            $notaFiscalServico = new NotaEmitida();
            $nota = $notaFiscalServico->adicionarNfse($novaNota);
            $urlNota = $this->consultarUrlNota($nota, null);
            $nota->url_view = $urlNota;
            $nota->save();
            //Utilitarios::sendMessage('Nota Emitida por: '. $empresaSessao->razao_social.'| Nota Número: ' . $numeroRps);
        }catch(\Exception $e){
            /*$detalhesErro = sprintf(
                "Erro Emissão NFS-e: %s | Código: %s | Arquivo: %s | Linha: %s\nTrace: %s",
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            );*/
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

    protected function emitir_substituicao($dados){
        $retorno = [];
        $rps = $this->gerarRPS($dados); 
        $xml = $this->gerarXmlSubstituicao204($dados, [$rps]);
        Log::info($xml);
        
        $resposta = $this->enviarRequisicao($xml, 'SubstituirNfse');
        $array = json_decode(json_encode($resposta), TRUE);  

        Log::info($array);
       
        if(isset($array['SubstituirNfseResponse']['SubstituirNfseResult']['ListaMensagemRetorno'])){
            $erro = $array['SubstituirNfseResponse']['SubstituirNfseResult']['ListaMensagemRetorno']['MensagemRetorno'];
            $mensagem = '';

            if( is_array($erro) && isset($erro['Codigo']) && isset($erro['Mensagem'])){
                $mensagem = "Erro: #" . $erro['Codigo']. ' - ' . $erro['Mensagem'];
                $mensagem .= ' Solução: Para Corrigir o Erro ' . $erro['Correcao'];
            }else{
                foreach($erro as $err){
                    $mensagem .= "Erro: #" . $err['Codigo']. ' - ' . $err['Mensagem'];
                    $mensagem .= ' Solução: Para Corrigir o Erro ' . $err['Correcao'];
                    $mensagem .= ' / ';
                }
            }

            $retorno = [
               'error' => true,
               'data' => null,
               'message' => $mensagem
            ];
        }else{
            $dados = $array['SubstituirNfseResponse']['SubstituirNfseResult']['RetSubstituicao'];
            $declaracao_servico_prestado = $dados['NfseSubstituidora']['CompNfse']['Nfse']['InfNfse']['DeclaracaoPrestacaoServico'];

            //NUM NOTA
            $numero_nota = $dados['NfseSubstituidora']['CompNfse']['Nfse']['InfNfse']['Numero'];
            $numero_rps = $declaracao_servico_prestado['InfDeclaracaoPrestacaoServico']['Rps']['IdentificacaoRps']['Numero'];
            $serie_rps = $declaracao_servico_prestado['InfDeclaracaoPrestacaoServico']['Rps']['IdentificacaoRps']['Serie'];
            $tipo_rps = $declaracao_servico_prestado['InfDeclaracaoPrestacaoServico']['Rps']['IdentificacaoRps']['Tipo'];

            //substituida
            /*$numero_nota_sub = $dados['NfseSubstituida']['CompNfse']['Nfse']['InfNfse']['Numero'];
            $numero_rps_sub = $declaracao_servico_prestado['InfDeclaracaoPrestacaoServico']['Rps']['IdentificacaoRps']['Numero'];
            $serie_rps_sub = $declaracao_servico_prestado['InfDeclaracaoPrestacaoServico']['Rps']['IdentificacaoRps']['Serie'];
            $tipo_rps_sub = $declaracao_servico_prestado['InfDeclaracaoPrestacaoServico']['Rps']['IdentificacaoRps']['Tipo'];*/
            
            $retorno =  [
                'error' => false,
                'data' => [
                    'numero_nota' => $numero_nota,
                    'numero_rps' => $numero_rps,
                    'serie' => $serie_rps,
                    'tipo' => $tipo_rps,
                    /*'numero_nota_sub' => $numero_nota_sub,
                    'numero_rps_sub' => $numero_rps_sub,
                    'serie_sub' => $serie_rps_sub,
                    'tipo_sub' => $tipo_rps_sub,*/
                ],
                'message' => null
            ];
        }
        
        return $retorno;
    }

    protected function emitir($dados){
        $retorno = [];
        $empresaSessao = request()->session()->get('empresa_selecionada');
        $empresaSessao = $this->empresaModel->find($empresaSessao);

        $rps = $this->gerarRPS($dados); 
        
        $xml = $this->gerarXmlAbrasf204([$rps]);

        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xml);
        $xml = str_replace('<?xml version="1.0"?>', ' ', trim($domxml->saveXML()));   
        $xml = ltrim($xml); 

        //Assinando Novamente o RPS
        $xmlNotaAssinado = $this->assinarRpsRepetidamente($xml, 'EnviarLoteRpsSincronoEnvio'); 
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xmlNotaAssinado);
        $xml = str_replace('<?xml version="1.0"?>', ' ', trim($domxml->saveXML()));   
        $xml = ltrim($xml);
        
        //quebra de linhas
        //$xml = str_replace("&#xD;", "\n\r", $xml);//quebra de linha na descrição da nota que estava errado.
        $xml = str_replace(
            ["&#xD;", "&#xA;", "\r\n", "\n", "\r"],
            "\n", 
            $xml
        );    
        
        Log::info($xml);

        //TRANSMITINDO O RPS
        $resposta = $this->enviarRequisicao($xml, 'RecepcionarLoteRpsSincrono');
        $array = json_decode(json_encode($resposta), TRUE);  
        //dd($array);
        
        //Log::info($array);

        if(isset($array['RecepcionarLoteRpsSincronoResponse']['EnviarLoteRpsSincronoResposta']['ListaMensagemRetorno'])){
            $erro = $array['RecepcionarLoteRpsSincronoResponse']['EnviarLoteRpsSincronoResposta']['ListaMensagemRetorno']['MensagemRetorno'];
            $mensagem = '';

            if( is_array($erro) && isset($erro['Codigo']) && isset($erro['Mensagem'])){
                $mensagem = "Erro: #" . $erro['Codigo']. ' - ' . $erro['Mensagem'];
                $mensagem .= ' Solução: Para Corrigir o Erro ' . $erro['Correcao'];
                DB::insert(
                        'INSERT INTO internal_logs (empresa_id, description) VALUES (?, ?)',
                        [
                            $empresaSessao->id,
                            $erro['Codigo'] . ' - ' . $erro['Mensagem'] . '->> Solução: ' . $erro['Correcao']
                        ]
                    );
            }else{
                $mensagem .= '<center><h1>ATENÇÃO:</h1></center><br />';
                foreach($erro as $err){
                    $mensagem .= '<b>'.$err['Codigo']. '</b> - ' . $err['Mensagem'].'<br />';
                    $mensagem .= '<b>Solução</b>: Para Corrigir o Erro ' . $err['Correcao'].'<br />';
                    $mensagem .= '<br />';

                    DB::insert(
                        'INSERT INTO internal_logs (empresa_id, description) VALUES (?, ?)',
                        [
                            $empresaSessao->id,
                            $err['Codigo'] . ' - ' . $err['Mensagem'] . '->> Solução: ' . $err['Correcao']
                        ]
                    );
                }
            }

            $retorno = [
               'error' => true,
               'data' => null,
               'message' => $mensagem
            ];
        }else{
            //Log::info($array);
            $dados = $array['RecepcionarLoteRpsSincronoResponse']['EnviarLoteRpsSincronoResposta'];
            $declaracao_servico_prestado = $dados['ListaNfse']['CompNfse']['Nfse']['InfNfse']['DeclaracaoPrestacaoServico'];

            //NUM NOTA
            $numero_nota = $dados['ListaNfse']['CompNfse']['Nfse']['InfNfse']['Numero'];
            $numero_rps = $declaracao_servico_prestado['InfDeclaracaoPrestacaoServico']['Rps']['IdentificacaoRps']['Numero'];
            $serie_rps = $declaracao_servico_prestado['InfDeclaracaoPrestacaoServico']['Rps']['IdentificacaoRps']['Serie'];
            $tipo_rps = $declaracao_servico_prestado['InfDeclaracaoPrestacaoServico']['Rps']['IdentificacaoRps']['Tipo'];
            
            $retorno =  [
                'error' => false,
                'data' => [
                    'numero_nota' => $numero_nota,
                    'numero_rps' => $numero_rps,
                    'serie' => $serie_rps,
                    'tipo' => $tipo_rps
                ],
                'message' => $array['RecepcionarLoteRpsSincronoResponse']['EnviarLoteRpsSincronoResposta']['Protocolo']
            ];
        }
        
        return $retorno;
    }

    public function confirmacaoTransmissao(){
        return view('notas.status-transmissao');
    }

    protected function gerarRPS($dados){
        $empresaSessao = request()->session()->get('empresa_selecionada');
        $empresaSessao = $this->empresaModel->find($empresaSessao);
        
        $codigoCnaePrincipal = $empresaSessao->cnaes()->find($dados['empresa_cnae_id'])->codigo_cnae;
        $codigoAtividadeMunicipio = $empresaSessao->atividadesEmpresa()->find($dados['empresa_atividade_id'])->codigo_atividade;
               
        //Construção do RPS
        $rps = new RpsClass();
        $tipoDoc = ($empresaSessao->getTipoPessoa($empresaSessao->cpf_cnpj) == 2 ? $rps::CNPJ: $rps::CPF);
        $rps->prestador($tipoDoc, $empresaSessao->cpf_cnpj, $empresaSessao->inscricao_municipal);
        
        //dados do tomador
        if(Session::get('tomador_selecionado') != null){
            $tomador = $this->tomadorModel->find(Session::get('tomador_selecionado'));
        }else{
            $tomador = $this->tomadorModel->find($dados['tomador_id']);
        }

        $tipoDoc = ($tomador->getTipoPessoa($tomador->cpf_cnpj) == 2 ? $rps::CNPJ: $rps::CPF);
        $rps->tomador($tipoDoc, 
            Utilitarios::limparCpfCnpj($tomador->cpf_cnpj),
            is_null($tomador->inscricao_municipal) ? '' : $tomador->inscricao_municipal,
            htmlspecialchars($tomador->razao_social), //correção - toda vez que o campo possuir caracteres especial como & que é usado ex: &nbsp vai gerar erro
            preg_replace('/[^0-9]/', '', $tomador->telefone1),
            $tomador->email,
        );
        
        //Tomador Endereço
        $rps->tomadorEndereco(
            $tomador->logradouro,
            empty($tomador->numero) ? 'S/N' : $tomador->numero,
            Utilitarios::somenteLetrasENumerosSemSimbolos($tomador->complemento),
            Utilitarios::somenteLetrasENumerosSemSimbolos($tomador->bairro),
            $tomador->cidade_id,
            $tomador->cidade()->first()->estado()->first()->sigla,
            preg_replace('/[^0-9]/', '', $tomador->cep)
        );
        
        if($tomador->cidade_id == "99999"){
            $rps->codigoPaisBacen($tomador->codigo_pais_bacen);
            $rps->codigo_nif($tomador->nif);
        }

        //dados RPS
        //Informações do Rps
        $rps->numero($empresaSessao->num_ultima_nota + 1);
        $rps->serie($empresaSessao->serie_nota);
        $rps->status($rps::STATUS_NORMAL);
        $rps->tipo($rps::TIPO_RPS);

        if(!empty($dados['nbs_id'])){
            $nbs = Nbs::where('id', $dados['nbs_id'])->first();
            $rps->codigoNbs($nbs->codigo_nbs);
        }

        //reter iss
        if(isset($dados['chkIssqnRetido']) && $dados['chkIssqnRetido'] == 'on'){
            $rps->issRetido($rps::SIM);
        }else{
            $rps->issRetido($rps::NAO);
        }
       
        $timezone = new \DateTimeZone('America/Sao_Paulo');
        $rps->dataEmissao(new \DateTime("now", $timezone));
        $rps->municipioPrestacaoServico($dados['cidade_id']);

        //Data Competencia - adicionado em 10/03/2026
        $data_competencia = new \DateTime(date('Y-m-d'));
        if (!empty($dados['data_competencia'] ?? null)) {
            $data_competencia = new \DateTime($dados['data_competencia']);
            $hoje = new \DateTime();

            if ($data_competencia > $hoje) {
                $data_competencia = new \DateTime(date('Y-m-d'));
            }
        }
        
        $rps->dataCompetencia($data_competencia);
        
        /*switch($dados['ddlNaturezaOperacao']){
            case 1: 
                $rps->naturezaOperacao($rps::NATUREZA_INTERNA);
                $rps->municipioIncidencia( $empresaSessao->cidade_id  );
                break;
            case 2: 
                $rps->naturezaOperacao($rps::NATUREZA_EXTERNA);
                $rps->municipioIncidencia( $tomador->cidade_id );
                break;
            default:
                $rps->naturezaOperacao($rps::NATUREZA_INTERNA);
                break;
        }*/

        //TESTE EM 12/03/2026
        if($dados['ddlNaturezaOperacao'] == 1){
            //Tributação Dentro do Município
            $rps->naturezaOperacao($rps::NATUREZA_INTERNA);
            $rps->municipioIncidencia( $empresaSessao->cidade_id  );
        }else{
            //Tributação Fora do Município
            $rps->naturezaOperacao($rps::NATUREZA_EXTERNA);
            $rps->municipioIncidencia( $tomador->cidade_id );
        }

        if(strlen($dados['item_lc_id']) == 4){
            $codigoItemLc = substr($dados['item_lc_id'],0,2).'.'.substr($dados['item_lc_id'],2,2);
        }else{
            if($dados['item_lc_id'] < 1000){
                $res = (string) str_replace('.', '', $dados['item_lc_id'] / 1000);
            }

            if(strlen($res) == 3){
                $res = $res . "0";
            }
            
            $codigoItemLc = substr($res,0,2).'.'.substr($res,2,2);
        }

        $rps->itemListaServico($codigoItemLc);
        $rps->codigoCnae($codigoCnaePrincipal);
        $rps->codigoTributacaoMunicipio($codigoAtividadeMunicipio); //atividade exercida

        //Descrição dos Serviços

        $rps->discriminacao(htmlspecialchars($dados['txtDescServicos']));//
        $rps->regimeEspecialTributacao($empresaSessao->regime_esp_tributacao); 
       
        //se for rps para substituição
        //$rps->rpsSubstituido('5555', 'A1', 1);

        $rps->optanteSimplesNacional( ($empresaSessao->is_optante_simples_nac == 1) ? $rps::SIM : $rps::NAO);
        $rps->incentivadorCultural($rps::NAO);
        $rps->informacoesComplementares(htmlspecialchars($dados['txtInfoComplementares']));
                
        //Valores dos Serviço
        $aliquota                       = $dados['txtAliq'];
        $totalServico                   = $dados['txtTotal'];
        $valorDeducacaoBaseCalculo 		= isset($dados['txtDeducaoBaseCalculo']) ? $dados['txtDeducaoBaseCalculo'] : 0.00;
        $valorPIS                       = $dados['txtPis'];
        $valorCofins                    = $dados['txtCofins'];
        $valorCSLL                      = $dados['txtCSLL'];
        $valorIRPF                      = $dados['txtIRRF'];
        $valorInss                      = $dados['txtOutros'];
        $valorOutrasRetencoes           = $dados['txtOutrasRetencoes'];
        $valorDescontoCondicionado      = isset($dados['txtDescontoCondicionado']) ? $dados['txtDeducaoBaseCalculo'] : 0.00;
        $valorDescontoIncondicionado    = isset($dados['txtDescontoInCondicionado']) ? $dados['txtDescontoInCondicionado'] : 0.00;
      
		$baseDeCalculo = $totalServico - $valorDeducacaoBaseCalculo - $valorDescontoIncondicionado;
		
        //$rps->aliquota($aliquota);
        $rps->valorServicos($totalServico);
        $rps->baseCalculo($baseDeCalculo); //(ValorServicos – ValorDeducoes – DescontoIncondicionado)
        $calculoValorIss = $baseDeCalculo * ($aliquota / 100);
        //$rps->valorDeducoes($valorDeducoes);
                
        if(isset($dados['chkIssqnRetido']) && $dados['chkIssqnRetido'] == 'on'){
            //$rps->responsavelRetencao(1);//tomador

            //retem - iss retido.
            if($dados['ddlNaturezaOperacao'] != 1){ 
                //tributa fora do municipio
                //Utilitarios::sendMessage('PNF - Situação ISS Retido e Tributação Fora do Município.');
                if( ($empresaSessao->cidade_id !=  $tomador->cidade_id) || $empresaSessao->is_optante_simples_nac == 1 ){
                    $rps->aliquota($aliquota);
                    $rps->valorIss($calculoValorIss);//Base de calculo*alíquota/100
                    //Utilitarios::sendMessage('PNF - Caso 1');
                }else{
                    if($aliquota > 0 ){
                        $rps->aliquota($aliquota);
                        $rps->valorIss($calculoValorIss);//Base de calculo*alíquota/100
                        //Utilitarios::sendMessage('PNF - Caso 2');
                    }else{
                        //nada a fazer
                        //Utilitarios::sendMessage('PNF - Caso 3');
                    }
                }
            }else{
                //Utilitarios::sendMessage('PNF - Situação ISS Retido e Tributação Dentro do Município.');

                //tributa dentro do municipio - quando reter e for dentro do municipio - calcula sozinho
                if($aliquota > 0 ){
                    $rps->aliquota($aliquota);
                    //$rps->valorIss($calculoValorIss);//Base de calculo*alíquota/100
                    //$rps->valorIss($calculoValorIss);//Base de calculo*alíquota/100
                    //Utilitarios::sendMessage('PNF - Caso 4');
                }else{
                    //se aliquota informada for 0 não preencher $rps->aliquota(xxxx);
                    //Utilitarios::sendMessage('PNF - Caso 5');
                }
            }
        }else{
            //não retem - iss não retido.
            if($dados['ddlNaturezaOperacao'] != 1){ 
                //tributa fora do municipio
                $rps->aliquota($aliquota);
                $rps->valorIss($calculoValorIss);//Base de calculo*alíquota/100
                //Utilitarios::sendMessage('PNF - Situação ISS não Retido e Tributação Fora do Município.');
                //dd('Nada Implementado para ISS Não Retido e Tributação Fora do Município');
                //Utilitarios::sendMessage('PNF - Caso 6');
            }else{
                //não envia campo aliquota
                //tributa dentro do municipio
                if($aliquota > 0 ){
                    $rps->aliquota($aliquota);

                    if($empresaSessao->cidade_id !=  $tomador->cidade_id){
                        $rps->valorIss($calculoValorIss);//Base de calculo*alíquota/100
                    }
                }else{
                    
                }
                //Utilitarios::sendMessage('PNF - Situação ISS não Retido e Tributação Dentro do Município.');
                //dd('Nada Implementado para ISS Não Retido e Tributação Dentro do Município');
                //Utilitarios::sendMessage('PNF - Caso 7');
            }
        }

        //Utilitarios::sendMessage('Empresa: '. $empresaSessao->cpf_cnpj .' / Tomador: '. $tomador->razao_social . ' / Total Serviço: R$ ' . $dados['txtTotal']);

        //$rps->valorIss($calculoValorIss);//Base de calculo*alíquota/100 //tem que remover esse campo se tem valor retido não pode preencher os dois

        //retenções de impostos
        $rps->valorPis($valorPIS);
        $rps->valorCofins($valorCofins);
        $rps->valorCsll($valorCSLL);
        $rps->valorInss($valorInss);
        $rps->valorIr($valorIRPF);
        $rps->outrasRetencoes($valorOutrasRetencoes);
        $rps->descontoCondicionado($valorDescontoCondicionado);
        $rps->descontoIncondicionado($valorDescontoIncondicionado);
        $rps->valorDeducoes($valorDeducacaoBaseCalculo);

        $totalRetencoesImpostos = $valorPIS; 
        $totalRetencoesImpostos += $valorCofins; 
        $totalRetencoesImpostos += $valorInss; 
        $totalRetencoesImpostos += $valorIRPF; 
        $totalRetencoesImpostos += $valorCSLL; 
        $totalRetencoesImpostos += $valorOutrasRetencoes;
        
        if(isset($dados['chkIssqnRetido'])){
            $totalRetencoesImpostos += $calculoValorIss;
        }

        $totalRetencoesImpostos += $valorDescontoCondicionado;
        $totalRetencoesImpostos += $valorDescontoIncondicionado;
        
        if($totalRetencoesImpostos != null){
            $rps->valorTotalTributos = Utilitarios::arredondarSispetro($totalRetencoesImpostos);
        }

        $valorFinalNota = $totalServico - $totalRetencoesImpostos;
        
        $rps->valorLiquidoNfse($valorFinalNota);
        
        return $rps;
    }

    public function visualizarXmlNota($id){
        $empresaSessao = Session::get('empresa_selecionada');
        $nota = $this->nfseModel
            ->where('empresa_id', $empresaSessao)
            ->find($id);
        
        $this->consultarXmlNota($nota);
    }

    public function cancelarNotaIssNet($id){
        $empresaSessao = Session::get('empresa_selecionada');
        $nota = $this->nfseModel
            ->where('empresa_id', $empresaSessao)
            ->find($id);

        if(is_null($nota)){
            session()->flash('danger', 'Opss! Nota não encontrada!');
            return redirect()->route('nota.index');
        }
        
        if (request()->isMethod('post')) {
            $dados = request()->all();
            $dados['num_nfse'] = $nota->num_nfse;

            $cancelamento = $this->cancelarNfse($dados);
        
            if(isset($cancelamento->RetCancelamento->NfseCancelamento->Confirmacao)){
                $nota->cancelada = 1;
                $nota->motivo_cancelamento = $dados['justificativa'];
                $nota->data_hora_cancel = date('Y-m-d G:i:s');
                $nota->save();

                session()->flash('danger', 'Nota Cancelada com Sucesso!');
                return redirect()->route('nota.index');
            }else{
                $erro = $cancelamento->ListaMensagemRetorno->MensagemRetorno;
                $mensagem = "Erro: #" . $erro->Codigo. ' - ' . $erro->Mensagem;
                $mensagem .= ' Solução: Para Corrigir o Erro ' . $erro->Correcao;

                session()->flash('danger', $mensagem);
                return redirect()->route('notas.cancelar-issnet', $nota->id);
            }
        }

        $motivo = [
            '1' =>  'Erro na emissão',
			'2' =>  'Serviço não prestado',
			//'3' =>  'Erro de assinatura', //uso da prefeitura
			'4' =>  'Duplicidade da nota',
			//'5' =>  'Erro de processamento', //uso da prefeitura
        ];

        return view('emissor.cancelar', compact('nota','motivo'));
    }

    public function getNotasEmpresa($empresaId){
        $notas = [];
        //set_time_limit(0);
        ini_set('memory_limit', '-1');

        if($empresaId != '0'){
            $empresa = $this->empresaModel->find($empresaId);
            request()->session()->put('dados_empresa', $empresa);
        }else{
            $empresa = Session::get('empresa_selecionada');
            $empresa = $this->empresaModel->find(Session::get('empresa_selecionada'));
        }

        $grupoDatas = null;
        $ultimasEmissao = Carbon::now()->subDays(30)->format('Y-m-d');
        $dataFinal = Carbon::parse($ultimasEmissao)->addDays(9)->format('Y-m-d');
        $grupoDatas[] = [$ultimasEmissao, $dataFinal];
        $grupoDatas = $this->calculoDatas($grupoDatas);

        $grupoDatas = array_filter($grupoDatas, function($var) use($empresa){
            if($var[0] == date('Y-m-d') && $var[1] == date('Y-m-d')){
                return false;
            }
            return true;
        });

        $grupoDatas[] = [
            0 => Carbon::now()->format('Y-m-d'),
            1 => Carbon::now()->format('Y-m-d')
        ];
        
        try{
            foreach($grupoDatas as $data){
                $res = $this->consultarNotasEmitidas($empresa->cpf_cnpj, $empresa->inscricao_municipal, $data);

                if(!empty($res))
                {
                    $notas[] = $res;
                }            
            }
            
            foreach($notas as $grpNotas){
                if(isset($grpNotas->ListaNfse)){
                    foreach($grpNotas->ListaNfse->CompNfse as $nota){
                        $nfse = $nota->Nfse->InfNfse;
                        //dd($nfse);
                        
                        //DADOS DO TOMADOR DO SERVIÇO
                        $docTomador = null;
                        $declaracao_servico_prestado = $nfse->DeclaracaoPrestacaoServico->InfDeclaracaoPrestacaoServico->TomadorServico;
                        
                        if(isset($declaracao_servico_prestado->IdentificacaoTomador->CpfCnpj->Cnpj)){
                            $docTomador = $declaracao_servico_prestado->IdentificacaoTomador->CpfCnpj->Cnpj;
                        }
                        else
                        {
                            $docTomador = $declaracao_servico_prestado->IdentificacaoTomador->CpfCnpj->Cpf;
                        }

                        $tomador = $this->tomadorModel
                            ->where('empresa_id', $empresa->id)
                            ->where('cpf_cnpj', $docTomador)->first();

                        if(is_null($tomador)){
                            $dadosTomador = Utilitarios::consultarEmpresaCNPJ($docTomador);
                            $tomador = new Tomador();
                            $dadosTomador['empresa_id'] = $empresa->id;
                            $municipio = Municipio::with('estado')
                                ->where('municipio', $dadosTomador['municipio'])
                                ->whereHas('estado', function ($query) use ($dadosTomador) {
                                    $query->where('sigla', $dadosTomador['uf']);
                                })
                                ->first();
                            $dadosTomador['cidade_id'] = $municipio->codigo;
                            $tomador->fill($dadosTomador);
                            
                            $tomador->save();

                            $tomador = $this->tomadorModel->where('cpf_cnpj', $docTomador)->first();
                        }

                        $novaNota = [
                            'empresa_id' => $empresa->id,
                            'tomador_id' => $tomador->id,
                            'num_nfse' => (string)$nfse->Numero,
                            //'numero_rps' => (int)$nfse->DeclaracaoPrestacaoServico->InfDeclaracaoPrestacaoServico->Rps->IdentificacaoRps->Numero,
                            'numero_rps' => isset($nfse->DeclaracaoPrestacaoServico->InfDeclaracaoPrestacaoServico->Rps->IdentificacaoRps->Numero)
        ? (int) $nfse->DeclaracaoPrestacaoServico->InfDeclaracaoPrestacaoServico->Rps->IdentificacaoRps->Numero
        : 0,
                            'cod_verificacao_nfse' =>  (string)$nfse->CodigoVerificacao,
                            'data_emissao_nfse' => date('Y-m-d', strtotime((string)  $nfse->DataEmissao)),
                            'competencia' => (string)$nfse->DeclaracaoPrestacaoServico->InfDeclaracaoPrestacaoServico->Competencia,
                            'valor_nota' => (float)$nfse->ValoresNfse->ValorLiquidoNfse,
                            'cod_trib_mun' =>(string) $nfse->DeclaracaoPrestacaoServico->InfDeclaracaoPrestacaoServico->Servico->CodigoTributacaoMunicipio,
                        ];

                        /*if(isset($nfse->DeclaracaoPrestacaoServico->InfDeclaracaoPrestacaoServico->Rps)){
                            $novaNota += [
                                'numero_rps' =>  (string)$nfse->DeclaracaoPrestacaoServico->InfDeclaracaoPrestacaoServico->Rps->IdentificacaoRps->Numero,
                                'serie_rps' => (string)$nfse->DeclaracaoPrestacaoServico->InfDeclaracaoPrestacaoServico->Rps->IdentificacaoRps->Serie,
                                'tipo_rps' => (string)$nfse->DeclaracaoPrestacaoServico->InfDeclaracaoPrestacaoServico->Rps->IdentificacaoRps->Tipo,
                                'data_emissao_rps' => date('Y-m-d', strtotime((string) $nfse->DeclaracaoPrestacaoServico->InfDeclaracaoPrestacaoServico->Rps->DataEmissao)),
                            ];
                        }else{
                            $novaNota += [
                                'numero_rps' =>  '-',
                                'serie_rps' => 8,
                                'tipo_rps' => 1,
                                'data_emissao_rps' => date('Y-m-d', strtotime((string)  $nfse->DataEmissao)),
                            ];
                        }*/
    
                        $notaFiscalServico = new NotaEmitida();
                        $notaIncluida = $notaFiscalServico->adicionarNfse($novaNota);
                        
                        /*DB::table('emitidas')->where('id', $notaIncluida->id)->update(
                            [
                                'created_at' => date('Y-m-d', strtotime((string)$nota->tcDataEmissaoRps)),
                            ]
                        );*/

                        $urlNota = $this->consultarUrlNota($notaIncluida, $empresa);
                        $notaIncluida->url_view = $urlNota;
                        
                        //Dados Nota - Cancelamento
                        /*$dadosCancelamento = $this->consultarNota($notaIncluida);
                        if(isset($dadosCancelamento['ListaNfse']['CompNfse']['NfseCancelamento'])){
                            $dadosCancelamento = $dadosCancelamento['ListaNfse']['CompNfse']['NfseCancelamento'];

                            $notaIncluida->cancelada = 1;
                            $notaIncluida->motivo_cancelamento = 'Nota Cancelada Via Sistema';

                            if(isset($dadosCancelamento['Confirmacao']['InfConfirmacaoCancelamento']['DataHora'])){
                                $notaIncluida->data_hora_cancel = date('Y-m-d', strtotime((string)$dadosCancelamento['Confirmacao']['InfConfirmacaoCancelamento']['DataHora']));
                            }
                        }*/

                        if(isset($nota->NfseCancelamento))
                        {
                            $notaIncluida->cancelada = 1;
                            $notaIncluida->motivo_cancelamento = 'Nota Cancelada Via Sistema';

                            if(isset($nota->NfseCancelamento->Confirmacao)){
                                $notaIncluida->data_hora_cancel = date('Y-m-d', strtotime((string)$nota->NfseCancelamento->Confirmacao->DataHora));
                            }
                        }
                        $notaIncluida->save();
                    }
                }
            }
        }catch(\Exception $e){
            dd($e->getMessage());
            //session()->flash('danger', 'Opss! Ocorreu um erro ao sincronizar as notas. Erro: ' . $e->getMessage());
        }

        return redirect()->route('nota.index');
    }

    public function sincronizarNotasRecentes($empresaId){
        ini_set('memory_limit', '-1');

        if($empresaId != '0'){
            $empresa = $this->empresaModel->find($empresaId);
            request()->session()->put('dados_empresa', $empresa);
        }else{
            $empresa = Session::get('empresa_selecionada');
            $empresa = $this->empresaModel->find(Session::get('empresa_selecionada'));
        }

        $grupoDatas = null;
       
        $ultimasEmissao = Carbon::now()->format('Y-m-d');
        $dataFinal = Carbon::parse($ultimasEmissao)->addDays(9)->format('Y-m-d');

        $grupoDatas[] = [$ultimasEmissao, $dataFinal];
        rsort($grupoDatas);

        foreach($grupoDatas as $data){
            $notas[] = $this->consultarNotasEmitidas($empresa->cpf_cnpj, $empresa->inscricao_municipal, $data); 
        }

        foreach($notas as $grpNotas){
            if(isset($grpNotas['ListaNfse'])){
                foreach($grpNotas['ListaNfse']['CompNfse'] as $nota){
                    if(isset($nota['tcInfNfse']) || isset($nota['tcNfse'])){
                        if(!isset($nota['tcNfse'])){
                            $nota = (object) $nota['tcInfNfse'];
                        }else{
                            $nota = (object) $nota['tcNfse']['tcInfNfse'];
                        }
                        
                        if(isset($nota->tcTomadorServico['tcIdentificacaoTomador'])){
                            $docTomador = $nota->tcTomadorServico['tcIdentificacaoTomador'];
                        
                            if(isset($docTomador['tcCpfCnpj']['tcCnpj'])){
                                $docTomador = $docTomador['tcCpfCnpj']['tcCnpj'];
                            }else{
                                $docTomador = $docTomador['tcCpfCnpj']['tcCpf'];
                            }
                        }

                        $tomador = $this->tomadorModel
                            ->where('empresa_id', $empresa->id)
                            ->where('cpf_cnpj', $docTomador)->first();

                        if(is_null($tomador)){
                            $dadosTomador = Utilitarios::consultarEmpresaCNPJ($docTomador);
                            $tomador = new Tomador();
                            $dadosTomador['empresa_id'] = $empresa->id;

                            $municipio = Municipio::with('estado')
                                ->where('municipio', $dadosTomador['municipio'])
                                ->whereHas('estado', function ($query) use ($dadosTomador) {
                                    $query->where('sigla', $dadosTomador['uf']);
                                })
                                ->first();
                            $dadosTomador['cidade_id'] = $municipio->codigo;

                            $tomador->fill($dadosTomador);
                            $tomador->save();

                            $tomador = $this->tomadorModel->where('cpf_cnpj', $docTomador)->first();
                        }

                        $novaNota = [
                            'empresa_id' => $empresa->id,
                            'tomador_id' => $tomador->id,
                            'num_nfse' => $nota->tcNumero,
                            'cod_verificacao_nfse' =>  $nota->tcCodigoVerificacao,
                            'data_emissao_nfse' => date('Y-m-d', strtotime((string)  $nota->tcDataEmissao)),
                            'numero_rps' =>  $nota->tcIdentificacaoRps['tcNumero'],
                            'serie_rps' => $nota->tcIdentificacaoRps['tcSerie'],
                            'tipo_rps' => $nota->tcIdentificacaoRps['tcTipo'],
                            'competencia' => (string)$nota->tcCompetencia,
                            'data_emissao_rps' => date('Y-m-d', strtotime((string)$nota->tcDataEmissaoRps)),
                            'valor_nota' => (float)$nota->tcServico['tcValores']['tcValorLiquidoNfse'],
                            'cod_trib_mun' => (string)$nota->tcServico['tcCodigoTributacaoMunicipio'],
                        ];
                        
                        $notaFiscalServico = new NotaEmitida();
                        $notaIncluida = $notaFiscalServico->adicionarNfse($novaNota);
                        
                        DB::table('emitidas')->where('id', $notaIncluida->id)->update(
                            [
                                'created_at' => date('Y-m-d', strtotime((string)$nota->tcDataEmissaoRps)),
                            ]
                        );

                        $urlNota = $this->consultarUrlNota($notaIncluida, $empresa);
                        $notaIncluida->url_view = $urlNota;
                        
                        //Dados Nota - Cancelamento
                        $dadosCancelamento = $this->consultarNota($notaIncluida);
                        if(isset($dadosCancelamento['ListaNfse']['CompNfse']['NfseCancelamento'])){
                            $dadosCancelamento = $dadosCancelamento['ListaNfse']['CompNfse']['NfseCancelamento'];

                            $notaIncluida->cancelada = 1;
                            $notaIncluida->motivo_cancelamento = 'Nota Cancelada Via Sistema';

                            if(isset($dadosCancelamento['Confirmacao']['InfConfirmacaoCancelamento']['DataHora'])){
                                $notaIncluida->data_hora_cancel = date('Y-m-d', strtotime((string)$dadosCancelamento['Confirmacao']['InfConfirmacaoCancelamento']['DataHora']));
                            }
                        }
                        $notaIncluida->save();
                    }      
                }
            }
        }
    }

    public function servicosTomados($empresaId = 0)
    {
        ini_set('memory_limit', '-1');

        if($empresaId != '0'){
            $empresa = $this->empresaModel->find($empresaId);
            request()->session()->put('dados_empresa', $empresa);
        }else{
            $empresa = Session::get('empresa_selecionada');
            $empresa = $this->empresaModel->find(Session::get('empresa_selecionada'));
        }

        $this->consultarServicoTomado($empresa->cpf_cnpj, $empresa->inscricao_municipal, ['2025-10-01', '2025-10-29']);
    }

    public function duplicar($id){
        $id = base64_decode($id);
        $original = $this->nfseModel->find($id);

        if($original->empresa_id != Session::get('empresa_selecionada')){
            session()->flash('danger', 'Sua empresa não emitiu está nota.');
            return redirect()->route('nota.index');
        }

        $dados_nota_original = $original->dados_emissao_json;

        if(is_null($dados_nota_original) || empty($dados_nota_original)){
            session()->flash('danger', 'Nâo há dados para duplicar a nota.');
            return redirect()->route('nota.index');
        }

        //preencher os dados
        $tomador = $this->tomadorModel->find($original->tomador_id);
        
        $empresa = $this->empresaModel->with(['cnaes', 'atividadesEmpresa'])
            ->find(Session::get('empresa_selecionada'));  
            
        $isMei = $empresa->is_mei ? 'display: none;' : '';
        
        $permiteDescontoCond = $empresa->permite_desc_cond == 2 ? 'display: none;' : '';
        $permiteDescontoInc = $empresa->permite_desc_incond == 2 ? 'display: none;' : '';
        $permiteDeducao = $empresa->permite_deducao == 2 ? 'display: none;' : '';

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
       
        /*$aliquotaAtividade = number_format($empresa->atividadesEmpresa()
            ->where('id', $empresa->empresa_atividade_id)
            ->first()
            ->aliquota, 2, ',', '');*/
        $aliquotaAtividade = null;
        
        //if(isset($dadosCadastrais->PermiteTributarFora) && $dadosCadastrais->PermiteTributarFora == 1){
            $listTributacao = [
                1 => 'Tributação no município',
                2 => 'Tributação fora do município'
            ];
        /*}else{
            $listTributacao = [
                1 => 'Tributação no município',
            ];
        }*/

        //cnaes
        $cnaes = $this->cnaeModel->cnaesList($empresa->id);
        //atividades
        $atividades = $this->atividadeModel->atividadesList($empresa->id);

        //filtro items lc conforme cnae
        $listaCnae = $this->cnaeModel->find($empresa->empresa_cnae_id);
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

        $data_competencia = date('Y-m-d');

        //substituindo dados para mostrar na tela
        $uf_id = $dados_nota_original['uf'];
        $empresa->cidade_id = $dados_nota_original['cidade_id'];
        
        $aliquotaAtividade = $dados_nota_original['txtAliq'] ?? $aliquotaAtividade;

        $camposFormatar = [
            "txtAliq",
            "txtDescontoCondicionado",
            "txtDescontoInCondicionado",
            "txtDeducaoBaseCalculo",
            "txtTotalISSQN",
            "txtPis",
            "txtCofins",
            "txtOutros",
            "txtIRRF",
            "txtCSLL",
            "txtISSQNResponsavel",
            "txtOutrasRetencoes",
            "txtValorDescontos",
            "txtTotalRetencoes",
            "txtTotalValorLiquido",
        ];
    
        foreach ($camposFormatar as $campo) {
            if (isset($dados_nota_original[$campo])) {

                // Normaliza para float (seguro para string ou número)
                $valor = $dados_nota_original[$campo];
                //$valor = str_replace('.', ',', $valor);
                //$valor = str_replace(',', '.', $valor);
                $numero = (float) $valor;

                // Aplica regra
                $dados_nota_original[$campo] = $numero > 0
                    ? number_format($numero, 2, ',', '.')
                    : null;
            }
        }

        $nbs_list = $this->nbsModel->getListaNbs($dados_nota_original['item_lc_id']);

        return view('emissor.create', [
            'data_competencia' => $data_competencia,
            'tomador' => $tomador,
            'empresa' => $empresa,
            'isMei' => $isMei,
            'permiteDescontoInc' => $permiteDescontoInc,
            'permiteDescontoCond' => $permiteDescontoCond,
            'permiteDeducao' => $permiteDeducao,
            'estados' => $estados,
            'uf_id' => $uf_id,
            'cidades' => $cidades,
            'dados_cadastrais' => $dadosCadastrais,
            'listTributacao' => $listTributacao,
            'aliquotaAtividade' => $aliquotaAtividade,
            'cnaes' => $cnaes,
            'atividades' => $atividades,
            'servicos' => $servicos,
            'nbs_list' => $nbs_list,
            'nota_original' => $dados_nota_original
        ]);
    }
}
