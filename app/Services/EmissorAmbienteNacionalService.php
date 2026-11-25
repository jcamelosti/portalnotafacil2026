<?php
namespace App\Services;

use App\Jobs\EnviarNfseEmailJob;
use App\Models\Certificado;
use App\Models\Empresa;
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

class EmissorAmbienteNacionalService
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
        $dados_form_original = $dados;
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

        //imovel no ibs/cbs
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
        
        $dados = new DPSDataSnDTO(
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
            aliquotaIss: number_format($dados['txtAliquota'] ?? null, 2, '.', ''),//string '2.5'
            
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
            atvEvento: $evento,
            imovelData: $imovelData ?? null,
            tpEntGov: $dados['ddlTpEntGov'] ?? null,
            tpOper: $dados['ddlTpOper'] ?? null,
        );

        $certificadoCliente = Certificado::where('empresa_id', Session::get('empresa_selecionada'))
            ->first();

        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $path = getenv("CAMINHO_CERTIFICADO_LOCAL");
        }else{
            $path = getenv("CAMINHO_CERTIFICADO_PROD");
        }
        
        $certificado = $path.$certificadoCliente->arquivo;
        
        try {
            $config = new \stdClass();
            $config->tpamb = $empresa->ambiente_emissao == 'HOMOLOGACAO' ? 2 : 1; //1 - Produção, 2 - Homologação
            
            $configJson = json_encode($config);
            
            $content = file_get_contents($certificado);
            $password = base64_decode($certificadoCliente->senha);

            $cert = \NFePHP\Common\Certificate::readPfx($content, $password);
            $tools = new \Hadder\NfseNacional\Tools($configJson, $cert);

            //gerar dps
            $std = $this->gerarDps($dados, $empresa);
            $dps = new \Hadder\NfseNacional\Dps($std);

            $xml = $dps->render();

            // 2. Cria o objeto DOMDocument
            $dom = new \DOMDocument('1.0', 'UTF-8');
            // 3. Ativa a formatação automática (identação)
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            // 4. Carrega a string XML
            $dom->loadXML($xml);

            // 6. Exibe o XML formatado
            //var_dump($dom->saveXML());
            //exit;
            Log::info("NFSE ADN");
            Log::info($dom->saveXML());
            //exit;
            //dd($dps);
            $response = $tools->enviaDpsAdn($dps->render(), 1);//2 para adn, 1 sefin -> 1 é padrão
            
            Log::info($response);
            
            if (isset($response['nfseXmlGZipB64'])) {
                $xmlNfse = gzdecode(base64_decode($response['nfseXmlGZipB64']));

                $empresa->num_ultimo_dps = $numeroProximoDPS; //recebo o ultimo emitido
                $empresa->save();

                Utilitarios::sendMessage("Nota SN - Emitida com Sucesso! \n\nEmpresa: {$empresa->razao_social} \nChave NFSe: {$response['chaveAcesso']}");

                Log::info($xmlNfse);

                $xml = simplexml_load_string($xmlNfse);
                $xml->registerXPathNamespace('nfse', 'http://www.sped.fazenda.gov.br/nfse');
                $nNFSe = (string) $xml->xpath('//nfse:nNFSe')[0];


                $ne = NotaEmitida::create([
                    'empresa_id' => $empresa->id,
                    'tomador_id' => $tomador->id,
                    'nfse_xml'   => $xmlNfse,
                    'num_nfse'   => $nNFSe,
                    'valor'      => $totalNfse,
                    'dados_emissao' => $dados_form_original,
                    'chave' => $response['chaveAcesso']
                ]);

                //session()->flash('success', 'Nota Fiscal Emitida com Sucesso.');

                if($empresa->disparar_email_tomador == 1){
                    EnviarNfseEmailJob::dispatch($ne->id);
                }
                
                return [
                    'error' => false,
                    'mensagem' => 'Nota Emitida com Sucesso',
                    'registro' => $ne
                ];
            } else {
                Log::info('Erros ao emitir NFSe: ' . json_encode($response));
                if (isset($response['erros']) && !empty($response['erros'])) {
                    $mensagemErro = '';
                    foreach ($response['erros'] as $erro) {
                        $mensagemErro .= $erro['Codigo'] . ' - ' . $erro['Descricao'] . "\n";

                        if(isset($erro['Complemento']) && !empty($erro['Complemento'])){
                            $mensagemErro .= 'Complemento: ' . $erro['Complemento'] . "\n";
                        }

                        DB::insert(
                            'INSERT INTO internal_logs (empresa_id, description) VALUES (?, ?)',
                            [
                                $empresa->id,
                                'Erros ao emitir NFSe: ' . $erro['Codigo'] . ' - ' . $erro['Descricao'] . (isset($erro['Complemento']) ? ' Complemento: ' . $erro['Complemento'] : ''),
                            ]
                        );
                    }
                    //return redirect()->back()->with('danger', 'Erros ao emitir NFSe: ' . $mensagemErro);
                    return [
                        'error' => true,
                        'mensagem' => 'Erros ao emitir NFSe: ' . $mensagemErro,
                        'registro' => null
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage() . ' ->> ' . $e->getFile(). '  linha  ' . $e->getLine());
            DB::insert(
                'INSERT INTO internal_logs (empresa_id, description) VALUES (?, ?)',
                [
                    $empresa->id,
                    $e->getMessage(),
                ]
            );
            return [
                'error' => true,
                'mensagem' => 'Erro ao emitir NFSe: ' . $e->getMessage(),
                'registro' => null
            ];
        }
    }

    protected function gerarDps($dados, $empresa){
        $std = new \stdClass();

        $ambienteEmissao =  $empresa->ambiente_emissao == "HOMOLOGACAO" ? 2 : 1;

        $calculoService = new CalculoTributosDpsService();
        $impostosFederais = $calculoService->calcular($dados);
        
        $std->infDPS = new \stdClass();
        $std->infDPS->tpAmb = $ambienteEmissao; // 1 - Produção, 2 - Homologação
        $std->infDPS->dhEmi = now()->format('Y-m-d\TH:i:sP'); // "Data e hora da emissão da DPS. AAAA-MM-DDThh:mm:ssTZD"
        $std->infDPS->verAplic = 'PortalNotaFacil_V1.0';
        $std->infDPS->serie = $dados->serie;
        $std->infDPS->nDPS = $empresa->num_ultimo_dps + 1;//ver qual é o último número
        $std->infDPS->dCompet = now()->format('Y-m-d'); //"Data de competência da prestação do serviço. Ano, Mês e Dia (AAAA-MM-DD)"
        $std->infDPS->tpEmit = 1; //Emitente da DPS: 1 - Prestador; 2 - Tomador; 3 - Intermediário;
        
        //não informar para produção $std->infDPS->finnfse
        if ((int) (getenv('LOCAL_ENV') ?: 0) === 1) {
            $std->infDPS->finnfse = 0; //Indicador da finalidade da emissão de NFS-e - 0 - NFS-e regular 1 - NFS-e de crédito 2 - NFS-e de débito
        }

        $std->infDPS->cLocEmi = $empresa->cidade_id; //Código IBGE 7 dígitos da cidade emissora da NFS-e.
        
        //Substituição
        //    $std->infDPS->subst = new stdClass();
        //    $std->infDPS->subst->chSubstda = 'DPS000000000000000000000000000000000000000000'; // Chave de Acesso da NFS-e a ser substituída.
        //    $std->infDPS->subst->cMotivo = '01'; // 01 - Desenquadramento de NFS-e do Simples Nacional; 02 - Enquadramento de NFS-e no Simples Nacional; 03 - Inclusão Retroativa de Imunidade/Isenção para NFS-e; 04 - Exclusão Retroativa de Imunidade/Isenção para NFS-e; 05 - Rejeição de NFS-e pelo tomador ou pelo intermediário se responsável pelo recolhimento do tributo; 99 - Outros;
        //    $std->infDPS->subst->xMotivo = 'Descreva o motivo'; // Descrição do motivo da substituição quando cMotivo = 9

        //Dados do Prestador de serviço
        $std->infDPS->prest = new \stdClass();
        $std->infDPS->prest->CNPJ = $empresa->cpf_cnpj;
        //    $std->infDPS->prest->CPF = '00000000000';
        //    $std->infDPS->prest->NIF = ''; // Número de identificação fiscal fornecido por órgão de administração tributária no exterior.
        //    $std->infDPS->prest->cNaoNIF = 0; // Motivo para não informação do NIF: 0 - Não informado na nota de origem; 1 - Dispensado do NIF; 2 - Não exigência do NIF;
        //    $std->infDPS->prest->CAEPF = '0'; // Número do Cadastro de Atividade Econômica da Pessoa Física (CAEPF) do prestador do serviço.
        //    $std->infDPS->prest->IM = '0'; // Número de inscrição municipal do prestador do serviço.
        //$std->infDPS->prest->xNome = 'Hadder Soft';
        $std->infDPS->prest->fone = preg_replace("/\D/", "", $empresa->telefone1);
        $std->infDPS->prest->email = $empresa->email;

        //O endereço nacional do prestador do serviço não deve ser informado na DPS quando o próprio prestador for o emitente da DPS.
        /*$std->infDPS->prest->end = new stdClass();
        $std->infDPS->prest->end->xLgr = 'DOM EMANUEL';
        $std->infDPS->prest->end->nro = '765';
        $std->infDPS->prest->end->xCpl = 'Apto 301 Bl. DA4 - Residencial Dom Afonso';
        $std->infDPS->prest->end->xBairro = 'MARACANA';
        $std->infDPS->prest->end->endNac = new stdClass();
        $std->infDPS->prest->end->endNac->cMun = '5201108'; //Código IBGE 7 dígitos
        $std->infDPS->prest->end->endNac->CEP = '75040440';*/

        //    $std->infDPS->prest->end->endExt = new stdClass();
        //    $std->infDPS->prest->end->endExt->cPais = ''; // Código do país do endereço do prestador do prestador do serviço. (Tabela de Países ISO)
        //    $std->infDPS->prest->end->endExt->cEndPost = ''; // Código alfanumérico do Endereçamento Postal no exterior do prestador do serviço.
        //    $std->infDPS->prest->end->endExt->xCidade = ''; // Nome da cidade no exterior do prestador do serviço.
        //    $std->infDPS->prest->end->endExt->xEstProvReg = ''; // Estado, província ou região da cidade no exterior do prestador do serviço.

        $std->infDPS->prest->regTrib = new \stdClass();
        $std->infDPS->prest->regTrib->opSimpNac = $empresa->op_simp_nac; // Situação perante Simples Nacional: 1 - Não Optante; 2 - Optante - Microempreendedor Individual (MEI); 3 - Optante - Microempresa ou Empresa de Pequeno Porte (ME/EPP);
        
        if($empresa->op_simp_nac == 3){
            $std->infDPS->prest->regTrib->regApTribSN = $empresa->tp_reg_apuracao_sn; // Regime de Apuração Tributária pelo Simples Nacional. 1 – Regime de apuração dos tributos pelo SN; 2 – Regime de apuração dos tributos federais pelo SN e o ISSQN pela NFS-e conforme respectiva legislação municipal; 3 – Regime de apuração dos tributos federais e municipal pela NFS-e conforme respectivas legilações federal e municipal de cada tributo;"
        }
        $std->infDPS->prest->regTrib->regEspTrib = $empresa->tp_regime_esp_trib_mun; // Tipos de Regimes: 0 - Nenhum; 1 - Ato Cooperado (Cooperativa); 2 - Estimativa; 3 - Microempresa Municipal; 4 - Notário ou Registrador; 5 - Profissional Autônomo; 6 - Sociedade de Profissionais;"

        
        /*
            DADOS TOMADOR
        */

        if($dados->cpfTomador !== '99999999999'){
            $std->infDPS->toma = new \stdClass();

            if(!empty($dados->cpfTomador) || !is_null($dados->cpfTomador)){
                $std->infDPS->toma->CPF = $dados->cpfTomador;
            }else{
                $std->infDPS->toma->CNPJ = $dados->cnpjTomador;
            }
            
            $std->infDPS->toma->xNome = $dados->razaoTomador; 

            $std->infDPS->toma->end = new \stdClass();
            $std->infDPS->toma->end->xLgr = $dados->logradouroTomador;
            $std->infDPS->toma->end->nro = $dados->numeroTomador;
            $std->infDPS->toma->end->xCpl = $dados->complementoTomador;
            $std->infDPS->toma->end->xBairro = $dados->bairroTomador;

            $std->infDPS->toma->end->endNac = new \stdClass();
            $std->infDPS->toma->end->endNac->cMun = $dados->codigoMunicipioTomador; //Código IBGE 7 dígitos
            $std->infDPS->toma->end->endNac->CEP = $dados->cepTomador;
        }        

        /*
        Serviços
        */
        /*Local de prestação do serviço*/
        $std->infDPS->serv = new \stdClass();
        $std->infDPS->serv->locPrest = new \stdClass();
        $std->infDPS->serv->locPrest->cLocPrestacao = $dados->localPrestacaoMunicipio;// Código da localidade da prestação do serviço.
        
        $std->infDPS->serv->cServ = new \stdClass();
        $std->infDPS->serv->cServ->cTribNac = $dados->codigoTributacaoNacional;//Código de tributação Nacional
        //$std->infDPS->serv->cServ->cTribMun = $dados->codigoServicoMunicipal; //Código de tributação municipal do ISSQN.
        $std->infDPS->serv->cServ->xDescServ = $dados->descricaoServico;

        //if ((int) (getenv('LOCAL_ENV') ?: 0) === 0) { //não enviar em homologacao
            $std->infDPS->serv->cServ->cNBS = $dados->codigoNbs;//'122051900';// Código NBS correspondente ao serviço prestado
        //}
        //$std->infDPS->serv->cServ->cIntContrib = str_pad($servico->id, 5, '0', STR_PAD_LEFT);;// Código interno do contribuinte - Utilizado para identificação da DPS no Sistema interno do Contribuinte

        /*
        Informação Complementar
        */
        $std->infDPS->serv->cServ->infoCompl = new \stdClass();
        $std->infDPS->serv->cServ->infoCompl->xInfComp = $dados->informacaoComplementar;

        /*
            VALORES
        */
        $std->infDPS->valores = new \stdClass();
        $std->infDPS->valores->vServPrest = new \stdClass();
        //    $std->infDPS->valores->vServPrest->vReceb = 0.0; //Valor monetário recebido pelo intermediário do serviço (R$).
        $std->infDPS->valores->vServPrest->vServ = $dados->valorServico; //Valor monetário do serviço (R$).

        $std->infDPS->valores->trib = new \stdClass();
        $std->infDPS->valores->trib->tribMun = new \stdClass();
        $std->infDPS->valores->trib->tribMun->tribISSQN = $dados->tributaIss; // Tributação do ISSQN sobre o serviço prestado: 1 - Operação tributável; 2 - Imunidade 3 - Exportação de serviço; 4 - Não Incidência;
        $std->infDPS->valores->trib->tribMun->tpRetISSQN = $dados->tipoRetencaoIss; //Tipo de retencao do ISSQN: 1 - Não Retido; 2 - Retido pelo Tomador; 3 - Retido pelo Intermediario;
        //std->infdps->valores->trib->tribmun->paliq
        $std->infDPS->valores->trib->tribMun->pAliq = $dados->aliquotaIss;

        //tributos federais
        //$std->infdps->valores->trib->tribfed

        //calculos 
        $std->infDPS->valores->trib->tribFed = new \stdClass();
        $std->infDPS->valores->trib->tribFed->piscofins = new \stdClass();
        $std->infDPS->valores->trib->tribFed->piscofins->cst = $dados->cstPisCofins;
        $std->infDPS->valores->trib->tribFed->piscofins->vcofins = $dados->valorCofins ?? null;
        $std->infDPS->valores->trib->tribFed->piscofins->vpis = $dados->valorPis ?? null;
        $std->infDPS->valores->trib->tribFed->piscofins->paliqpis = $dados->aliquotaPis ?? null;
        $std->infDPS->valores->trib->tribFed->piscofins->paliqcofins = $dados->aliquotaCofins ?? null;
        $std->infDPS->valores->trib->tribFed->piscofins->tpretpiscofins = $dados->tipoRetencaoPisCofins;

        $retencoes = Utilitarios::obterTributosRetidos($dados->tipoRetencaoPisCofins);

        if(floatval($dados->valorRetencaoCp) > 0){
            $std->infDPS->valores->trib->tribFed->vretcp = $dados->valorRetencaoCp;
        }
        
        if(floatval($dados->valorRetencaoIrrf) > 0){
            $std->infDPS->valores->trib->tribFed->vretirrf = $dados->valorRetencaoIrrf;
        }

        if($retencoes['csll']){
            $std->infDPS->valores->trib->tribFed->vretcsll = $dados->valorRetencaoCsll;
        }
                
        //campos ibs/cbs
        //dd($dados);
        $std->infDPS->ibscbs = new \stdClass();
        if ((int) (getenv('LOCAL_ENV') ?: 0) === 0) {
            $std->infDPS->ibscbs->finNFSe = 0;
        }
        $std->infDPS->ibscbs->indfinal = 0;
        $std->infDPS->ibscbs->cIndOp = $dados->cIndOp;
        $std->infDPS->ibscbs->indDest = 0;//0 – o destinatário é o próprio tomador/adquirente identificado na NFS-e (tomador = adquirente = destinatário);
        $std->infDPS->ibscbs->tpOper = $dados->tpOper;
        $std->infDPS->ibscbs->tpEntGov = $dados->tpEntGov;

        //imovel no ibs/cbs
        if(isset($dados->imovelData) && !is_null($dados->imovelData)){
            $std->infDPS->ibscbs->imovel = new \stdClass();                
            $std->infDPS->ibscbs->imovel->inscImobFisc = $dados->imovelData->inscImobFisc;
            $std->infDPS->ibscbs->imovel->end = new \stdClass();                
        
            if(is_null($dados->imovelData->end->cPais)){
                $std->infDPS->ibscbs->imovel->end->endNac = new \stdClass();                
                $std->infDPS->ibscbs->imovel->end->endNac->cMun = $dados->imovelData->end->cMun;
                $std->infDPS->ibscbs->imovel->end->endNac->CEP = $dados->imovelData->end->CEP;
            }else{
                $std->infDPS->ibscbs->imovel->end->endExt = new \stdClass();                
                $std->infDPS->ibscbs->imovel->end->endExt->cPais = $dados->imovelData->end->cPais;
                $std->infDPS->ibscbs->imovel->end->endExt->cEndPost = $dados->imovelData->end->cEndPost;
                $std->infDPS->ibscbs->imovel->end->endExt->xCidade = $dados->imovelData->end->xCidade;
                $std->infDPS->ibscbs->imovel->end->endExt->xEstProvReg = $dados->imovelData->end->xEstProvReg;
            }

            $std->infDPS->ibscbs->imovel->end->xLgr = $dados->imovelData->end->xLgr;
            $std->infDPS->ibscbs->imovel->end->nro = $dados->imovelData->end->nro;
            $std->infDPS->ibscbs->imovel->end->xCpl = $dados->imovelData->end->xCpl;
            $std->infDPS->ibscbs->imovel->end->xBairro = $dados->imovelData->end->xBairro;
        }

        //valores 
        $std->infDPS->ibscbs->valores = new \stdClass();
        $std->infDPS->ibscbs->valores->trib = new \stdClass();

        //errado em produção
        //$std->infDPS->ibscbs->valores->trib->cClassTrib = $dados->cClassTrib;
        //$std->infDPS->ibscbs->valores->trib->cst = $dados->cstIbsCbs;
               
        $std->infDPS->ibscbs->valores->trib->gibscbs = new \stdClass();
        $std->infDPS->ibscbs->valores->trib->gibscbs->cClassTrib = $dados->cClassTrib;
        $std->infDPS->ibscbs->valores->trib->gibscbs->cst = $dados->cstIbsCbs;
        
        //<!-- Demais campos dependem do gTribRegular ou outros regimes -->
        /*
        <!-- Demais campos dependem do gTribRegular ou outros regimes -->
        <gTribRegular>
            <!-- CST Geral do IBS e CBS (Ex: 000 = Regular Integral) -->
            <CSTReg>000</CSTReg>
        </gTribRegular>
        */

        //final ibs/cbs

        //dd($std->infDPS->ibscbs->valores);
        //Para ME/EPP o indicador de informação de valor total de tributos não pode ser informado.
        $std->infDPS->valores->trib->totTrib = new \stdClass();
        //$std->infDPS->valores->trib->totTrib->indTotTrib = 0;

        $std->infDPS->valores->trib->totTrib->ptottribsn = $dados->percentualTotalTributos;
        
        return $std;
    }

    public function cancelar(array $dados, NotaEmitida $nota){
        $empresa = $this->empresaModel->with(['atividadesEmpresa'])
            ->find(Session::get('empresa_selecionada'));

        $std = new \stdClass();
        // Atenção: no seu código, 'npedregevento' é lido diretamente na raiz de $this->std
        $std->npedregevento = '001'; // Ou '1', conforme o método generatePre() espera

        // Grupo infPedReg
        $std->infpedreg = new \stdClass();
        $std->infpedreg->tpamb     = $empresa->ambiente_emissao == 'HOMOLOGACAO' ? 2 : 1; //1 - Produção, 2 - Homologação // 1 = Produção | 2 = Homologação
        $std->infpedreg->veraplic  = '1.0.0';
        $std->infpedreg->dhevento  = date('Y-m-d\TH:i:sP'); // Ex: 2026-11-20T10:22:00-03:00

        // Informe apenas CNPJAutor OU CPFAutor (não ambos)
        $std->infpedreg->cnpjautor = $empresa->cpf_cnpj;
        // $std->infpedreg->cpfautor = '00000000000';

        // Chave de acesso de 50 dígitos da NFS-e a ser cancelada
        $std->infpedreg->chnfse = substr($dados['chave_nfse'], 3);

        // Grupo do evento de cancelamento (e101101)
        $std->infpedreg->e101101 = new \stdClass();
        $std->infpedreg->e101101->xdesc   = 'Cancelamento de NFS-e';
        $std->infpedreg->e101101->cmotivo = $dados['motivo']; // 1 = Erro na Emissão | 2 = Serviço não Prestado | 9 = Outros
        $std->infpedreg->e101101->xmotivo = $dados['justificativa'];
        
        $certificadoCliente = Certificado::where('empresa_id', Session::get('empresa_selecionada'))
            ->first();

        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $path = getenv("CAMINHO_CERTIFICADO_LOCAL");
        }else{
            $path = getenv("CAMINHO_CERTIFICADO_PROD");
        }
        
        $certificado = $path.$certificadoCliente->arquivo;
        
        try {
            $config = new \stdClass();
            $config->tpamb = $empresa->ambiente_emissao == 'HOMOLOGACAO' ? 2 : 1; //1 - Produção, 2 - Homologação
            
            $configJson = json_encode($config);
            
            $content = file_get_contents($certificado);
            $password = base64_decode($certificadoCliente->senha);

            $cert = \NFePHP\Common\Certificate::readPfx($content, $password);
            $tools = new \Hadder\NfseNacional\Tools($configJson, $cert);
            //$response = $tools->cancelaNfse($std);
            $response = [
                "tipoAmbiente" => 2,
                "versaoAplicativo" => "SefinNacional_1.6.0",
                "dataHoraProcessamento" => "2026-11-23T15:36:08.8146246-03:00",
                "eventoXmlGZipB64" => "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"
            ];
                
            if (isset($response['eventoXmlGZipB64'])) {
                $xmlNfse = gzdecode(base64_decode($response['eventoXmlGZipB64']));
                $dadosCancelamento = $this->extrairDadosCancelamento($xmlNfse);
                
                Utilitarios::sendMessage("Nota SN - Cancelada com Sucesso! \n\nEmpresa: {$empresa->razao_social}");

                Log::info($xmlNfse);

                $nota->cancelada = 1;
                $nota->data_cancelamento = Carbon::parse($dadosCancelamento['data_processamento'])
                    ->format('Y-m-d H:i:s');
                $nota->chave = $dadosCancelamento['pedido']['chave_nfse'];
                $nota->motivo_cancelamento = $dadosCancelamento['pedido']['motivo'];

                $nota->save();
                    
                return [
                    'error' => false,
                    'mensagem' => 'Nota Cancelada com Sucesso',
                    'registro' => $nota
                ];
            } else {
                Log::info('Erros ao emitir NFSe: ' . json_encode($response));
                if (isset($response['erros']) && !empty($response['erros'])) {
                    $mensagemErro = '';
                    foreach ($response['erros'] as $erro) {
                        $mensagemErro .= $erro['Codigo'] . ' - ' . $erro['Descricao'] . "\n";

                        if(isset($erro['Complemento']) && !empty($erro['Complemento'])){
                            $mensagemErro .= 'Complemento: ' . $erro['Complemento'] . "\n";
                        }

                        DB::insert(
                            'INSERT INTO internal_logs (empresa_id, description) VALUES (?, ?)',
                            [
                                $empresa->id,
                                'Erros ao emitir NFSe: ' . $erro['Codigo'] . ' - ' . $erro['Descricao'] . (isset($erro['Complemento']) ? ' Complemento: ' . $erro['Complemento'] : ''),
                            ]
                        );
                    }
                    //return redirect()->back()->with('danger', 'Erros ao emitir NFSe: ' . $mensagemErro);
                    return [
                        'error' => true,
                        'mensagem' => 'Erros ao emitir NFSe: ' . $mensagemErro,
                        'registro' => null
                    ];
                }
            }
        } catch (\Exception $e) {
            DB::insert(
                'INSERT INTO internal_logs (empresa_id, description) VALUES (?, ?)',
                [
                    $empresa->id,
                    $e->getMessage(),
                ]
            );
            return [
                'error' => true,
                'mensagem' => 'Erro ao emitir NFSe: ' . $e->getMessage(),
                'registro' => null
            ];
        }
    }

    protected function extrairDadosCancelamento(string $xml): array
    {
        $dom = new \DOMDocument();

        $dom->preserveWhiteSpace = false;

        if (!$dom->loadXML($xml, LIBXML_NONET)) {
            throw new \RuntimeException(
                'XML inválido.'
            );
        }

        $xpath = new \DOMXPath($dom);

        $xpath->registerNamespace(
            'nfse',
            'http://www.sped.fazenda.gov.br/nfse'
        );

        $get = function (string $query) use ($xpath) {
            $node = $xpath->query($query)->item(0);

            return $node ? trim($node->nodeValue) : null;
        };

        return [
            'id_evento' => $get(
                '//nfse:infEvento/@Id'
            ),

            'versao' => $get(
                '//nfse:evento/@versao'
            ),

            'aplicacao' => $get(
                '//nfse:verAplic'
            ),

            'ambiente' => $get(
                '//nfse:ambGer'
            ),

            'sequencial' => $get(
                '//nfse:nSeqEvento'
            ),

            'data_processamento' => $get(
                '//nfse:dhProc'
            ),

            'numero_dfse' => $get(
                '//nfse:nDFSe'
            ),

            'pedido' => [
                'id' => $get(
                    '//nfse:infPedReg/@Id'
                ),

                'ambiente' => $get(
                    '//nfse:infPedReg/nfse:tpAmb'
                ),

                'data_evento' => $get(
                    '//nfse:dhEvento'
                ),

                'cnpj_autor' => $get(
                    '//nfse:CNPJAutor'
                ),

                'chave_nfse' => $get(
                    '//nfse:chNFSe'
                ),

                'codigo_evento' => $get(
                    '//*[local-name()="e101101"]/*[local-name()="cMotivo"]'
                ),

                'descricao_evento' => $get(
                    '//*[local-name()="e101101"]/*[local-name()="xDesc"]'
                ),

                'motivo' => $get(
                    '//*[local-name()="e101101"]/*[local-name()="xMotivo"]'
                ),
            ],
        ];
    }
}