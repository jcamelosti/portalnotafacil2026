<?php
namespace App\Services;

use App\Models\Empresa;
use App\Models\Municipio;
use App\Models\NotaEmitida;
use App\Models\Tomador;
use App\Utilitarios\Utilitarios;
use Illuminate\Support\Facades\DB;
use JCamelo\NfseNacionalLib\DTO\ComExtDTO;
use JCamelo\NfseNacionalLib\DTO\DPSDataSnDTO;
use JCamelo\NfseNacionalLib\Services\NFSeService;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use JCamelo\NfseNacionalLib\DTO\EnderecoDTO;
use JCamelo\NfseNacionalLib\DTO\EnderecoImovelDTO;
use JCamelo\NfseNacionalLib\DTO\EnderecoObraDTO;
use JCamelo\NfseNacionalLib\DTO\EventoDTO;
use JCamelo\NfseNacionalLib\DTO\ImovelDataDTO;
use JCamelo\NfseNacionalLib\DTO\ObraDataDTO;

class EmissorNotaService
{
    private NFSeService $nfse;
    private $empresaModel;
    private $tomadorModel;
    
    public function __construct(NFSeService $nfse, Empresa $empresaModel, Tomador $tomadorModel)
    {
        $this->nfse = $nfse;
        $this->empresaModel = $empresaModel;
        $this->tomadorModel = $tomadorModel;
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

    public function emitir($dados){
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
    
        $totalNfse = (float)$dados['txtTotal'];
        $totalComex = isset($dados['comex_vserv_moeda']) ? $dados['comex_vserv_moeda'] : null;

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
        $comExt = null;
        if($tomador->cidade()->first()->codigo == '99999' || $dados['ddlTribISSQN'] == 3){
            $totalComex = (float) $totalComex;

            $comExt = new ComExtDTO(
                mdPrestacao: $dados['comex_modo_prestacao'],
                vincPrest: $dados['comex_vinc_prest'],
                tpMoeda: $dados['comex_tipo_moeda'],
                vServMoeda: number_format($totalComex, 2, '.', ''),
                mecAFComexP: '01',
                mecAFComexT: '01',
                movTempBens: 1,
                nDI: null,
                nRE: null,
                mdic: 0,
            );
        }

        $evento = null;
        if(
            isset($dados['nome_evento']) && trim($dados['nome_evento']) !== '' &&
            isset($dados['dtini_evento']) && trim($dados['dtini_evento']) !== '' &&
            isset($dados['dtfim_evento']) && trim($dados['dtfim_evento']) !== '' &&
            isset($dados['txtCepEvento']) && trim($dados['txtCepEvento']) !== '' &&
            isset($dados['txtEnderecoEvento']) && trim($dados['txtEnderecoEvento']) !== '' &&
            isset($dados['txtNumeroEvento']) && trim($dados['txtNumeroEvento']) !== '' &&
            isset($dados['txtComplementoEvento']) && trim($dados['txtComplementoEvento']) !== '' &&
            isset($dados['txtBairroEvento']) && trim($dados['txtBairroEvento']) !== '' &&
            isset($dados['txtUFEvento']) && trim($dados['txtUFEvento']) !== '' &&
            isset($dados['txtCidadeEvento']) && trim($dados['txtCidadeEvento']) !== ''
        ){
            $evento = new EventoDTO(
                xNome: $dados['nome_evento'],
                dataInicio: $dados['dtini_evento'],
                dataFim: $dados['dtfim_evento'],
                end: new EnderecoDTO(
                    cMun: $dados['txtCidadeEvento'],
                    CEP: preg_replace('/[^0-9]/', '', $dados['txtCepEvento']),
                    xLgr: $dados['txtEnderecoEvento'],
                    nro: $dados['txtNumeroEvento'],
                    xCpl: $dados['txtComplementoEvento'],
                    xBairro: $dados['txtBairroEvento'],
                )
            );
        }

        $obra = null;
        if(
            isset($dados['txtEnderecoObras']) && !empty($dados['txtEnderecoObras']) &&
            isset($dados['txtNumeroObras']) && !empty($dados['txtNumeroObras']) &&
            isset($dados['txtComplementoObras']) && !empty($dados['txtComplementoObras']) &&
            isset($dados['txtUFObras']) && !empty($dados['txtUFObras']) &&
            isset($dados['txtCidadeObras']) && !empty($dados['txtCidadeObras']) &&
            isset($dados['txtCepObras']) && !empty($dados['txtCepObras']) &&
            isset($dados['txtBairroObras']) && !empty($dados['txtBairroObras'])
        ){
            $obra = new ObraDataDTO(
                inscImobFisc: null,
                nProcessoObra: null,
                end: new EnderecoObraDTO(
                    cMun: $dados['txtCidadeObras'],
                    CEP: preg_replace('/[^0-9]/', '', $dados['txtCepObras']),
                    xLgr: $dados['txtEnderecoObras'],
                    nro: $dados['txtNumeroObras'],
                    xCpl: $dados['txtComplementoObras'],
                    xBairro: $dados['txtBairroObras'],
                )
            );
        }

        $imovelData = null;
        if(
            isset($dados['txtEnderecoImovel']) && !empty($dados['txtEnderecoImovel']) &&
            //isset($dados['txtNumeroImovel']) && !empty($dados['txtNumeroImovel']) &&
            isset($dados['txtComplementoImovel']) && !empty($dados['txtComplementoImovel']) &&
            isset($dados['txtUFImovel']) && !empty($dados['txtUFImovel']) &&
            isset($dados['txtCidadeImovel']) && !empty($dados['txtCidadeImovel']) &&
            isset($dados['txtCepImovel']) && !empty($dados['txtCepImovel']) &&
            isset($dados['txtBairroImovel']) && !empty($dados['txtBairroImovel'])
        ) {
            $imovelData = new ImovelDataDTO(
                inscImobFisc: $dados['txtinscImobFisc'],
                end: new EnderecoImovelDTO(
                    cMun: $dados['txtCidadeImovel'],
                    CEP: preg_replace('/[^0-9]/', '', $dados['txtCepImovel']),
                    xLgr: $dados['txtEnderecoImovel'],
                    nro: $dados['txtNumeroImovel'],
                    xCpl: $dados['txtComplementoImovel'],
                    xBairro: $dados['txtBairroImovel'],
                )
            );
        }

        $numeroProximoDPS = $empresa->num_ultimo_dps + 1;
        
        $dataSN = new DPSDataSnDTO(
            ambiente: $empresa->ambiente_emissao == 'HOMOLOGACAO' ? 2 : 1,
            //dataEmissao: Carbon::now('America/Sao_Paulo')->format('Y-m-d\TH:i:sP'),
            dataEmissao:Carbon::parse($dados['data_competencia'])
                ->setTimeFromTimeString(now()->format('H:i:s'))
                ->format('Y-m-d\TH:i:sP'),
            serie: $empresa->serie_dps,
            numDps: $numeroProximoDPS,
            dataCompetencia: Carbon::now(
                'America/Sao_Paulo'
            )->format('Y-m-d'),
            codigoMunicipio: $dados['ddlCidadePrestacao'],
            //prestador
            cnpjPrestador: $empresa->cpf_cnpj,
            imPrestador: $empresa->inscricao_municipal,
            fonePrestador: preg_replace('/[^0-9]/', '', $empresa->telefone1) ?? null,
            emailPrestador: $empresa->email ?? null,
            municipioPrestador: $empresa->cidade_id,

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
            numeroTomador: $tomador->numero ?? '0',
            complementoTomador: $tomador->complemento ?? null,
            bairroTomador: $tomador->bairro ?? null,
            foneTomador: preg_replace('/[^0-9]/', '', $tomador->telefone1) ?? null,
            emailTomador: $tomador->email ?? null,
            
            //dados nif
            nif: $tomador->nif,
            nao_nif: $tomador->nao_nif,
            //endereço exterior
            endNoExterior: ($tomador->cidade()->first()->codigo == '99999') ? 1 : 2,
            pais: ($tomador->cidade()->first()->codigo == '99999') ? $tomador->pais : null,
            endPostal: ($tomador->cidade()->first()->codigo == '99999') ? $tomador->cep : null,
            cidade: ($tomador->cidade()->first()->codigo == '99999') ? $tomador->cidade : null,
            provincia: ($tomador->cidade()->first()->codigo == '99999') ? $tomador->provincia : null,
            //final endereço exterior

            //Campos ComExt - Tipo Operaçao Exportação ou quando informando o campo de endereço no exterior
            comExt: $comExt,
        
            //dados sobre o serviço
            codigoTributacaoNacional: $dados['cTribNac'],
            codigoServicoMunicipal: $dados['empresa_atividade_id'],
            descricaoServico: $dados['txtDescServicos'],
            codigoNbs: $dados['nbs'],
            codigoMunicipioPrestacao: $localPrestacao, //Local da Prestação de Serviço
            localPrestacaoPais: $dados['ddlPaisPrestacao'],
            localPrestacaoEstado: $dados['ddlEstadoPrestacao'],
            localPrestacaoMunicipio: $dados['ddlCidadePrestacao'],
            valorServico: number_format($totalNfse, 2, '.', ''),
            paisResultadoPrestacao: $dados['ddlPaisResult'] ?? null,
                        
            //issqn
            tributaIss: $dados['ddlTribISSQN'],
            tipoRetencaoIss: $dados['ddlTipoRetencao'] ?? null,
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
            tipoInfoTributos: $dados['ddlTipoInfo'],
            percentualTotalTributos: number_format($dados['txtPercentualTribSN'] ?? 0.00, 2, '.', ''),

            //valores ou percentuais tributos federal, estadual ou municipal
            percentualTribFederal: ($dados['ddlTipoInfo'] == 1) ? $dados['txtFederal'] ?? null : null,
            percentualTribEstadual: ($dados['ddlTipoInfo'] == 1) ? $dados['txtEstadual'] ?? null : null,
            percentualTribMunicipal: ($dados['ddlTipoInfo'] == 1) ? $dados['txtMunicipal'] ?? null : null,

            valorTribFederal: ($dados['ddlTipoInfo'] == 2) ? $dados['txtFederal'] ?? null : null,
            valorTribEstadual: ($dados['ddlTipoInfo'] == 2) ? $dados['txtEstadual'] ?? null : null,
            valorTribMunicipal: ($dados['ddlTipoInfo'] == 2) ? $dados['txtMunicipal'] ?? null : null,

            finNfse: 0,
            cIndOp: $dados['ddlIndicadorOperacao'],
            indDest: 0,
            cstIbsCbs: $dados['ddlSituacaoTributaria'],
            cClassTrib: $dados['ddlClassificacaoTributaria'],
            informacaoComplementar: $dados['txtInfoComplementares'] ?? null,

            //dados da obra
            obra: $obra,
            //dados do evento
            atvEvento: $evento,
            imovelData: $imovelData,
            tpEntGov: $dados['ddlTpEntGov'] ?? null,
            tpOper: $dados['ddlTpOper'] ?? null,
        );
        
        //validar se houver erro voltar para tela de emissão
        $validacaoRet = $this->nfse->validarXml($empresa->sigla_provedor, $dataSN, $empresa->id);
        
        if ($validacaoRet === null) {
            Log::error('Não foi possível interpretar o retorno da validação XML.', [
                'empresa_id' => $empresa->id,
            ]);
            throw new \DomainException('Não foi possível validar o Xml Gerado! Geralmente ocorre por uma falha de comunicação com o Servidor da Prefeitura(Issnet). Tente novamente.');
        } else {
            $body = $validacaoRet->Body ?? null;

            $resposta = $body?->ValidarXmlResponse ?? null;

            // Salva a resposta da validação quando existir
            if ($resposta !== null) {
                DB::insert(
                    'INSERT INTO validacoes_xml (empresa_id, xml) VALUES (?, ?)',
                    [
                        $empresa->id,
                        $resposta->asXML(),
                    ]
                );
            }

            $mensagens = $resposta
                ?->ValidarXmlResposta
                ?->ListaMensagemRetorno
                ?? null;

            if ($mensagens !== null) {
                $listaMensagens = $mensagens->MensagemRetorno ?? [];

                foreach ($listaMensagens as $mensagem) {
                    $codigo = (string) ($mensagem->Codigo ?? '');

                    // Registra somente mensagens de erro
                    if ($codigo !== '' && $codigo !== 'S000') {
                        DB::insert(
                            'INSERT INTO internal_logs (empresa_id, description) VALUES (?, ?)',
                            [
                                $empresa->id,
                                json_encode(
                                    $mensagem,
                                    JSON_UNESCAPED_UNICODE
                                ),
                            ]
                        );
                    }
                }
            }
        }
        
        //Gerar NFSe
        $retorno = $this->nfse->gerarNfse($empresa->sigla_provedor, $dataSN, $empresa->id);
    
        if($retorno === null || empty($retorno)){
            throw new \DomainException('A transmissão da NFS-e com DPS Nº: #<b>'. $dataSN->numDps . '</b> não foi efetivada! Geralmente ocorre por uma falha de comunicação com o Servidor da Prefeitura(Issnet). Tente novamente.');
        }

        if(isset($retorno->sBody->GerarNfseResponse->GerarNfseResposta->ListaMensagemRetorno)){
            $retornoLista = $retorno->sBody->GerarNfseResponse->GerarNfseResposta->ListaMensagemRetorno;
            $mensagens = [];
            $mensagemTexto = '<center><h1>ATENÇÃO:</h1></center><br />';

            foreach ($retornoLista->MensagemRetorno as $retorno) {
                $mensagens[] = [
                    'codigo'   => (string) $retorno->Codigo,
                    'mensagem' => (string) $retorno->Mensagem,
                    'correcao' => (string) $retorno->Correcao,
                ];

                $mensagemTexto .= '<b>'.(string) $retorno->Codigo. '</b> - ' . (string) $retorno->Mensagem.'<br />';
                $mensagemTexto .= '<b>Solução</b>: Para Corrigir o Erro ' . (string) $retorno->Correcao.'<br />';
                $mensagemTexto .= '<br />';

                DB::insert(
                    'INSERT INTO internal_logs (empresa_id, description) VALUES (?, ?)',
                    [
                        $empresa->id,
                        (string) $retorno->Codigo . ' - ' . $retorno->Mensagem . ' - ' . $retorno->Correcao
                    ]
                );
            }

            if(!empty($mensagens)){
                throw new \DomainException($mensagemTexto);
            }
        }
        
        $nNfse = (int)$retorno
            ->sBody
            ->GerarNfseResponse
            ->GerarNfseResposta
            ->ListaNfse
            ->CompNfse
            ->NFSe
            ->infNFSe
            ->nNFSe;

        $nDps = (int)$retorno
            ->sBody
            ->GerarNfseResponse
            ->GerarNfseResposta
            ->ListaNfse
            ->CompNfse
            ->NFSe
            ->infNFSe
            ->DPS->infDPS->nDPS;

        $empresa->num_ultimo_dps = $nDps;
        $empresa->save();

        $xmlNfse = $this->obterXml($empresa, $nNfse);

        $ne = NotaEmitida::create([
            'empresa_id' => $empresa->id,
            'tomador_id' => $tomador->id,
            'nfse_xml'   => $xmlNfse,
            'num_nfse'   => $nNfse,
            'valor'      => $totalNfse,
            'dados_emissao' => $dados
        ]);

        Utilitarios::sendMessage('Nota Emitida por: '. $empresa->razao_social.'| Nota Número: ' . $nNfse . ' | DPS: ' . $nDps);
        
        return [
            'error' => false,
            'Mensagem' => 'Nota Nº: '. $nNfse .' Emitida com Sucesso',
            'registro' => $ne
        ];
    }

    protected function obterXml(Empresa $empresa, $nNfse){
        $response = $this->nfse->consultarXml(
            $empresa->sigla_provedor,
            $empresa->id,
            $empresa->cpf_cnpj,//cnpj            
            $empresa->inscricao_municipal, //im,
            $nNfse, //nNFSe,
            '',//dt ini
            ''//dt fim
        );

        $xml = $response->
            sBody->
            ConsultarNfseServicoPrestadoResponse->
            ConsultarNfseServicoPrestadoResposta
            ->asXml();
        
        $domxml = new \DOMDocument('1.0', 'UTF-8');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xml);

        // FORÇA a codificação depois do loadXML()
        $domxml->encoding = 'UTF-8';
        
        $root = $domxml->documentElement;
        $root->setAttribute(
            'xmlns',
            'http://www.sped.fazenda.gov.br/nfse'
        );
        $xml = $domxml->saveXML($domxml->documentElement);
        $xml =  str_replace('<?xml version="1.0"?>', '', $xml);
        
        return $xml;
    }

    public function consultarUrlNota(Empresa $empresa, string $nNfse){
        $response = $this->nfse->consultarUrlNfse(
            $empresa->sigla_provedor,
            $empresa->id,
            $empresa->cpf_cnpj,//cnpj            
            $empresa->inscricao_municipal, //im,
            $nNfse, //nNFSe,
            '',//dt ini
            ''//dt fim
        );

        $retorno = null;
        $mensagem = '';

        if (
            isset($response['ConsultarUrlNfseResponse']['ConsultarUrlNfseResposta']['ListaMensagemRetorno']['MensagemRetorno'])
        ) {
            $erro = $response['ConsultarUrlNfseResponse']
                ['ConsultarUrlNfseResposta']
                ['ListaMensagemRetorno']
                ['MensagemRetorno'];

            // Quando vier apenas uma mensagem
            if (
                isset($erro['Codigo']) &&
                isset($erro['Mensagem'])
            ) {
                $mensagem = "Erro: #" . $erro['Codigo'] . ' - ' . $erro['Mensagem'];

                if (isset($erro['Correcao'])) {
                    $mensagem .= ' Solução: ' . $erro['Correcao'];
                }
            }
            // Quando vierem várias mensagens
            elseif (is_array($erro)) {
                foreach ($erro as $err) {
                    if (isset($err['Codigo']) && isset($err['Mensagem'])) {
                        $mensagem .= "Erro: #" . $err['Codigo'] . ' - ' . $err['Mensagem'];

                        if (isset($err['Correcao'])) {
                            $mensagem .= ' Solução: ' . $err['Correcao'];
                        }

                        $mensagem .= ' / ';
                    }
                }

                $mensagem = rtrim($mensagem, ' /');
            }
        }

        $retorno = [
            'error' => true,
            'data' => null,
            'message' => $mensagem
        ];

        return $retorno;
    }

    /**
     * Consulta as NFS-e prestadas no período e importa as que ainda não existem.
     * Quando chamada fora de uma requisição (Job), informe a $empresa.
     */
    public function consultarServicosPrestados(array $dados, ?Empresa $empresa = null){
        $empresa = $empresa ?? $this->empresaModel->find(Session::get('empresa_selecionada'));

        if (is_null($empresa)) {
            return ['error' => true, 'data' => null, 'message' => 'Empresa não encontrada.'];
        }

        $retorno = $this->nfse->consultarNfseServicosPrestados(
            'issnet',
            $empresa->id,
            $empresa->cpf_cnpj,//cnpj
            $empresa->inscricao_municipal,
            null,
            $dados['data_inicio'],//dt ini
            $dados['data_fim'],//dt fim
            null
        );
        
        if ($retorno instanceof \SimpleXMLElement) {
            $retorno = $retorno->asXML();
        }

        if (empty($retorno) || !is_string($retorno)) {
            return ['error' => true, 'data' => null, 'message' => 'Retorno vazio do provedor.'];
        }

        $ns = 'http://www.sped.fazenda.gov.br/nfse';

        $dom = new \DOMDocument('1.0', 'UTF-8');
        // Mantém os espaços originais: o XML contém assinaturas digitais
        $dom->preserveWhiteSpace = true;

        if (!@$dom->loadXML(trim($retorno))) {
            return ['error' => true, 'data' => null, 'message' => 'Não foi possível interpretar o XML de retorno.'];
        }

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('n', $ns);

        // Retorna o texto do primeiro nó encontrado (ou null)
        $valor = function (string $query, ?\DOMNode $contexto = null) use ($xpath) {
            $nodes = $contexto ? $xpath->query($query, $contexto) : $xpath->query($query);
            return ($nodes !== false && $nodes->length > 0) ? trim($nodes->item(0)->textContent) : null;
        };

        // Mensagens de erro do provedor
        $mensagensErro = $xpath->query('//n:ListaMensagemRetorno/n:MensagemRetorno');
        if ($mensagensErro->length > 0) {
            $mensagem = [];
            foreach ($mensagensErro as $msg) {
                $texto = 'Erro: #' . $valor('n:Codigo', $msg) . ' - ' . $valor('n:Mensagem', $msg);
                if ($correcao = $valor('n:Correcao', $msg)) {
                    $texto .= ' Solução: ' . $correcao;
                }
                $mensagem[] = $texto;
            }
            return ['error' => true, 'data' => null, 'message' => implode(' / ', $mensagem)];
        }

        $listaComp = $xpath->query('//n:ListaNfse/n:CompNfse');

        if ($listaComp->length == 0) {
            return ['error' => false, 'data' => [], 'message' => 'Nenhuma nota encontrada no período.'];
        }

        // Notas já gravadas para a empresa (evita duplicidade)
        $notasExistentes = NotaEmitida::where('empresa_id', $empresa->id)
            ->pluck('num_nfse')
            ->map(fn ($n) => (string) $n)
            ->flip();

        // Empresas do usuário dono da empresa (tomadores "do usuário")
        $empresasDoUsuario = $this->empresaModel
            ->where('user_id', $empresa->user_id)
            ->pluck('id')
            ->all();

        $cacheTomadores = [];
        $insertBatch = [];
        $ignoradas = 0;
        $agora = Carbon::now()->format('Y-m-d H:i:s');

        DB::beginTransaction();

        try {
            foreach ($listaComp as $comp) {
                $nfseNode = $xpath->query('n:NFSe', $comp)->item(0);
                if (!$nfseNode) {
                    continue;
                }

                // Ignora notas fora do padrão nacional
                if (!$this->isNfsePadraoNacional($nfseNode, $xpath, $valor, $empresa)) {
                    $ignoradas++;
                    Log::warning('NFS-e ignorada (fora do padrão nacional)', [
                        'empresa_id' => $empresa->id,
                        'nNFSe' => $valor('n:infNFSe/n:nNFSe', $nfseNode),
                    ]);
                    continue;
                }

                $inf = $xpath->query('n:infNFSe', $nfseNode)->item(0);
                $infDps = $xpath->query('n:DPS/n:infDPS', $inf)->item(0);

                $nNfse = $valor('n:nNFSe', $inf);

                if (isset($notasExistentes[$nNfse])) {
                    continue;
                }

                // Monta o XML da NFSe isolado, com o namespace no elemento raiz.
                // Obs.: não usar importNode — o PHP renomeia a <Signature> para "default:Signature".
                $xmlNfse = $dom->saveXML($nfseNode);
                if (!preg_match('/^<NFSe\b[^>]*\sxmlns="/', $xmlNfse)) {
                    $xmlNfse = preg_replace('/^<NFSe\b/', '<NFSe xmlns="' . $ns . '"', $xmlNfse, 1);
                }
                $xmlNfse = '<?xml version="1.0" encoding="utf-8"?>' . $xmlNfse;

                // Tomador
                $tomaNode = $infDps ? $xpath->query('n:toma', $infDps)->item(0) : null;
                $tomador = $tomaNode
                    ? $this->obterOuCriarTomadorXml($tomaNode, $xpath, $valor, $empresa, $empresasDoUsuario, $cacheTomadores)
                    : null;

                $vServ = $infDps ? $valor('n:valores/n:vServPrest/n:vServ', $infDps) : null;
                $dhEmi = $infDps ? $valor('n:dhEmi', $infDps) : null;
                $dataEmissao = $dhEmi ?? $valor('n:dhProc', $inf);
            
                $idNfse = $inf->getAttribute('Id');
                $insertBatch[] = [
                    'empresa_id' => $empresa->id,
                    'tomador_id' => $tomador ? $tomador->id : null,
                    'nfse_xml'   => $xmlNfse,
                    'num_nfse'   => $nNfse,
                    'valor'      => (float) ($vServ ?? $valor('n:valores/n:vLiq', $inf) ?? 0),
                    'chave'      => $idNfse ? preg_replace('/^NFS/', '', $idNfse) : null,
                    'dados_emissao' => null,
                    'cancelada' => 0,
                    'motivo_cancelamento' => null,
                    'data_cancelamento' => null,
                    'created_at' => $dataEmissao
                        ? Carbon::parse($dataEmissao)->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s')
                        : $agora,
                    'updated_at' => $agora,
                ];

                $notasExistentes[$nNfse] = true;
            }

            if (!empty($insertBatch)) {
                NotaEmitida::insert($insertBatch);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao importar serviços prestados: ' . $e->getMessage(), ['empresa_id' => $empresa->id]);

            return ['error' => true, 'data' => null, 'message' => 'Erro ao importar as notas: ' . $e->getMessage()];
        }

        $mensagem = count($insertBatch) . ' nota(s) importada(s) com sucesso.';
        if ($ignoradas > 0) {
            $mensagem .= ' ' . $ignoradas . ' nota(s) ignorada(s) por estarem fora do padrão nacional.';
        }

        return [
            'error' => false,
            'data' => array_column($insertBatch, 'num_nfse'),
            'ignoradas' => $ignoradas,
            'message' => $mensagem
        ];
    }

    /**
     * Verifica se a <NFSe> segue o leiaute nacional:
     * <NFSe versao xmlns="http://www.sped.fazenda.gov.br/nfse">
     *   <infNFSe Id="NFS + 50 dígitos"> nNFSe, dhProc, emit, valores,
     *     <DPS><infDPS Id="DPS + 42 dígitos"> dhEmi, prest, serv, valores
     * e se foi emitida pela própria empresa.
     */
    protected function isNfsePadraoNacional(\DOMElement $nfse, \DOMXPath $xpath, callable $valor, Empresa $empresa): bool
    {
        if ($nfse->namespaceURI !== 'http://www.sped.fazenda.gov.br/nfse' || !$nfse->hasAttribute('versao')) {
            return false;
        }

        $inf = $xpath->query('n:infNFSe', $nfse)->item(0);
        if (!$inf || !preg_match('/^NFS\d{50}$/', $inf->getAttribute('Id'))) {
            return false;
        }

        $infDps = $xpath->query('n:DPS/n:infDPS', $inf)->item(0);
        if (!$infDps || !preg_match('/^DPS\d{42}$/', $infDps->getAttribute('Id'))) {
            return false;
        }

        $obrigatorios = [
            [$inf, 'n:nNFSe'],
            [$inf, 'n:dhProc'],
            [$inf, 'n:emit'],
            [$inf, 'n:valores'],
            [$infDps, 'n:dhEmi'],
            [$infDps, 'n:prest'],
            [$infDps, 'n:serv'],
            [$infDps, 'n:valores/n:vServPrest/n:vServ'],
        ];

        foreach ($obrigatorios as [$contexto, $caminho]) {
            if ($xpath->query($caminho, $contexto)->length === 0) {
                return false;
            }
        }

        // A nota precisa ter sido emitida pela empresa consultada
        $docEmitente = $valor('n:emit/n:CNPJ', $inf) ?? $valor('n:emit/n:CPF', $inf);
        $docEmpresa = preg_replace('/\D/', '', (string) $empresa->cpf_cnpj);

        return $docEmitente === $docEmpresa;
    }

    /**
     * Localiza o tomador pelo CPF/CNPJ entre os tomadores globais ou das empresas
     * do usuário dono da empresa. Se não existir, cadastra a partir do XML (<toma>).
     */
    protected function obterOuCriarTomadorXml(
        \DOMNode $toma,
        \DOMXPath $xpath,
        callable $valor,
        Empresa $empresa,
        array $empresasDoUsuario,
        array &$cache
    ): ?Tomador {
        $documento = $valor('n:CNPJ', $toma) ?? $valor('n:CPF', $toma);
        $nif = $valor('n:NIF', $toma);

        $chaveCache = $documento ?? ('NIF:' . $nif);
        if (isset($cache[$chaveCache])) {
            return $cache[$chaveCache];
        }

        if (empty($documento) && empty($nif)) {
            return null;
        }

        $tomador = $this->tomadorModel
            ->where(function ($q) use ($documento, $nif) {
                if (!empty($documento)) {
                    $q->where('cpf_cnpj', $documento);
                } else {
                    $q->where('nif', $nif);
                }
            })
            ->where(function ($q) use ($empresasDoUsuario) {
                $q->whereIn('empresa_id', $empresasDoUsuario) // pertence ao usuário
                    ->orWhereNull('empresa_id')                 // global
                    ->orWhere('cpf_cnpj', '99999999999');       // global (consumidor)
            })
            // prioriza o tomador da própria empresa
            ->orderByRaw('CASE WHEN empresa_id = ? THEN 0 ELSE 1 END', [$empresa->id])
            ->first();

        if (is_null($tomador)) {
            $endExt = $xpath->query('n:end/n:endExt', $toma)->item(0);

            $dadosTomador = [
                'empresa_id'          => $empresa->id,
                'razao_social'        => $valor('n:xNome', $toma),
                'cpf_cnpj'            => $documento ?? 'NAO_INFORMADO',
                'inscricao_municipal' => $valor('n:IM', $toma),
                'email'               => $valor('n:email', $toma),
                'telefone1'           => $valor('n:fone', $toma),
                'logradouro'          => $valor('n:end/n:xLgr', $toma),
                'numero'              => $valor('n:end/n:nro', $toma),
                'complemento'         => $valor('n:end/n:xCpl', $toma),
                'bairro'              => $valor('n:end/n:xBairro', $toma) ?? 'NAO_INFORMADO',
                'nif'                 => $nif,
            ];

            if ($endExt) {
                $dadosTomador['cidade_id'] = '99999';
                $dadosTomador['cep']       = $valor('n:cEndPost', $endExt) ?? '00000000';
                $dadosTomador['pais']      = $valor('n:cPais', $endExt);
                $dadosTomador['cidade']    = $valor('n:xCidade', $endExt);
                $dadosTomador['provincia'] = $valor('n:xEstProvReg', $endExt);
            } else {
                $dadosTomador['cidade_id'] = $valor('n:end/n:endNac/n:cMun', $toma);
                $dadosTomador['cep']       = $valor('n:end/n:endNac/n:CEP', $toma);
            }

            $tomador = $this->tomadorModel->newInstance();
            $tomador->fill($dadosTomador);
            $tomador->save();
        }

        return $cache[$chaveCache] = $tomador;
    }
}
