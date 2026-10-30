<?php
namespace App\Services;

use App\Models\Certificado;
use App\Models\Empresa;
use App\Models\Tomador;
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

            $response = $tools->enviaDpsAdn($dps->render(), 2);//2 para adn, 1 sefin -> 1 é padrão
            dd($response);            
            Log::info($response);
            //    $response = [
            //        "tipoAmbiente" => 1,
            //        "versaoAplicativo" => "SefinNacional_1.3.0",
            //        "dataHoraProcessamento" => "2024-10-29T17:50:37.2687546-03:00",
            //        "idDps" => "NFS43030042228189466000104000000000004624101791626748",
            //        "chaveAcesso" => "43030042228189466000104000000000004624101791626748",
            //        "nfseXmlGZipB64" => "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",
            //        "alertas" => null
            //    ];
            if (isset($response['nfseXmlGZipB64'])) {
                /*$xml = gzdecode(base64_decode($response['nfseXmlGZipB64']));
                $servico->chave_nfse = $response['chaveAcesso'];
                $servico->data_emissao = now()->format('Y-m-d');
                $servico->save();

                $ult_rps = $empresa->num_ultimo_dps + 1;
                $empresa->num_ultimo_dps = $ult_rps;
                $empresa->save();
                Utilitarios::sendMessage("Nota MEI - Emitida com Sucesso! \n\nEmpresa: {$empresa->razao_social} \nChave NFSe: {$servico->chave_nfse}");
                session()->flash('success', 'Nota Fiscal Emitida com Sucesso.');
                return redirect()->route('servicos-mei.show', $servico->id);*/
            } else {
                /*Log::info('Erros ao emitir NFSe: ' . json_encode($response));
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
                }*/
            }
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getFile(), $e->getLine());
            /*DB::insert(
                'INSERT INTO internal_logs (empresa_id, description) VALUES (?, ?)',
                [
                    $empresa->id,
                    $e->getMessage(),
                ]
            );
            return redirect()->back()->with('danger', 'Erro ao emitir NFSe: ' . $e->getMessage());*/
        }
    }

    protected function gerarDps($dados, $empresa){
        $std = new \stdClass();

        $ambienteEmissao =  $empresa->ambiente_emissao == "HOMOLOGACAO" ? 2 : 1;
        
        $std->infDPS = new \stdClass();
        $std->infDPS->tpAmb = $ambienteEmissao; // 1 - Produção, 2 - Homologação
        $std->infDPS->dhEmi = now()->format('Y-m-d\TH:i:sP'); // "Data e hora da emissão da DPS. AAAA-MM-DDThh:mm:ssTZD"
        $std->infDPS->verAplic = 'PortalNotaFacil_V1.0';
        $std->infDPS->serie = 900;
        $std->infDPS->nDPS = $empresa->num_ultimo_dps + 1;//ver qual é o último número
        $std->infDPS->dCompet = now()->format('Y-m-d'); //"Data de competência da prestação do serviço. Ano, Mês e Dia (AAAA-MM-DD)"
        $std->infDPS->tpEmit = 1; //Emitente da DPS: 1 - Prestador; 2 - Tomador; 3 - Intermediário;
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
        $std->infDPS->serv->cServ->cNBS = $dados->codigoNbs;//'122051900';// Código NBS correspondente ao serviço prestado
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

        //$std->infDPS->valores->trib->totTrib = new \stdClass();
        //$std->infDPS->valores->trib->totTrib->indTotTrib = 0;

        return $std;
    }
}