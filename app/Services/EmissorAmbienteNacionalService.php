<?php
namespace App\Services;

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
use JCamelo\NfseNacionalLib\DTO\EnderecoObraDTO;
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

        $dados['txtNumeroObras'] = '150';
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
                    'dados_emissao' => $dados,
                    'chave' => $response['chaveAcesso']
                ]);

                //session()->flash('success', 'Nota Fiscal Emitida com Sucesso.');
                
                return [
                    'error' => false,
                    'Mensagem' => 'Nota Emitida com Sucesso',
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
                    return redirect()->back()->with('danger', 'Erros ao emitir NFSe: ' . $mensagemErro);
                }
            }
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getFile(), $e->getLine());
            DB::insert(
                'INSERT INTO internal_logs (empresa_id, description) VALUES (?, ?)',
                [
                    $empresa->id,
                    $e->getMessage(),
                ]
            );
            return redirect()->back()->with('danger', 'Erro ao emitir NFSe: ' . $e->getMessage());
        }
    }

    protected function gerarDps($dados, $empresa){
        $std = new \stdClass();

        $ambienteEmissao =  $empresa->ambiente_emissao == "HOMOLOGACAO" ? 2 : 1;
        
        $std->infDPS = new \stdClass();
        $std->infDPS->tpAmb = $ambienteEmissao; // 1 - Produção, 2 - Homologação
        $std->infDPS->dhEmi = now()->format('Y-m-d\TH:i:sP'); // "Data e hora da emissão da DPS. AAAA-MM-DDThh:mm:ssTZD"
        $std->infDPS->verAplic = 'PortalNotaFacil_V1.0';
        $std->infDPS->serie = $dados->serie;
        $std->infDPS->nDPS = $empresa->num_ultimo_dps + 1;//ver qual é o último número
        $std->infDPS->dCompet = now()->format('Y-m-d'); //"Data de competência da prestação do serviço. Ano, Mês e Dia (AAAA-MM-DD)"
        $std->infDPS->tpEmit = 1; //Emitente da DPS: 1 - Prestador; 2 - Tomador; 3 - Intermediário;
        $std->infDPS->finnfse = 0; //Indicador da finalidade da emissão de NFS-e - 0 - NFS-e regular 1 - NFS-e de crédito 2 - NFS-e de débito

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

        /*
        Serviços
        */
        /*Local de prestação do serviço*/
        $std->infDPS->serv = new \stdClass();
        $std->infDPS->serv->locPrest = new \stdClass();
        $std->infDPS->serv->locPrest->cLocPrestacao = $dados->localPrestacaoMunicipio;// Código da localidade da prestação do serviço.

        $std->infDPS->serv->cServ = new \stdClass();
        $std->infDPS->serv->cServ->cTribNac = $dados->codigoTributacaoNacional;//Código de tributação Nacional
        //$std->infDPS->serv->cServ->cTribMun = ''; //Código de tributação municipal do ISSQN.
        $std->infDPS->serv->cServ->xDescServ = $dados->descricaoServico;
        //$std->infDPS->serv->cServ->cNBS = $dados->codigoNbs;//'122051900';// Código NBS correspondente ao serviço prestado
        //$std->infDPS->serv->cServ->cIntContrib = str_pad($servico->id, 5, '0', STR_PAD_LEFT);;// Código interno do contribuinte - Utilizado para identificação da DPS no Sistema interno do Contribuinte

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

        //Para ME/EPP o indicador de informação de valor total de tributos não pode ser informado.
        $std->infDPS->valores->trib->totTrib = new \stdClass();
        //$std->infDPS->valores->trib->totTrib->indTotTrib = 0;

        $std->infDPS->valores->trib->totTrib->ptottribsn = $dados->percentualTotalTributos;

        return $std;
    }
}