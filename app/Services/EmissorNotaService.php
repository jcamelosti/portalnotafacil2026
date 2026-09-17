<?php
namespace App\Services;

use App\Models\Empresa;
use App\Models\Tomador;
use Illuminate\Support\Facades\DB;
use JCamelo\NfseNacionalLib\DTO\ComExtDTO;
use JCamelo\NfseNacionalLib\DTO\DPSDataSnDTO;
use JCamelo\NfseNacionalLib\Services\NFSeService;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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
        //$empresa->num_ultimo_dps = 14;
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
            valorServico: number_format($totalNfse, 2, '.', ''),
                        
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
        );

        //dd($dados, $comExt, $dataSN);
        
        //validar Xml
        $validacaoRet = $this->nfse->validarXml($empresa->sigla_provedor, $dataSN, $empresa->id);
        if(!isset($validacaoRet->sBody->ValidarXmlResponse->ValidarXmlResposta->ListaMensagemRetorno->MensagemRetorno->Codigo) 
            && (string)$validacaoRet->sBody->ValidarXmlResponse->ValidarXmlResposta->ListaMensagemRetorno->MensagemRetorno->Codigo != 'S000'){
            /*Log::info('FALHA - AVISO VALIDAÇÃO XML');
            Log::info(json_encode($validacaoRet->sBody->ValidarXmlResponse->ValidarXmlResposta->ListaMensagemRetorno, true));*/
            DB::insert(
                'INSERT INTO internal_logs (empresa_id, description) VALUES (?, ?)',
                [
                    $empresa->id,
                    json_encode($validacaoRet->sBody->ValidarXmlResponse->ValidarXmlResposta->ListaMensagemRetorno, true)
                ]
            );
        }
        
        //Gerar NFSe
        $retorno = $this->nfse->gerarNfse($empresa->sigla_provedor, $dataSN, $empresa->id);
        
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

        $empresa->num_ultimo_dps = (int)$nNfse;
        $empresa->save();

        $xmlNfse = $this->obterXml($empresa, $nNfse);

        Log::info($xmlNfse);
        dd($xmlNfse);
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
        $root = $domxml->documentElement;
        $root->setAttribute(
            'xmlns',
            'http://www.sped.fazenda.gov.br/nfse'
        );
        $xml = $domxml->saveXML();
        $xml =  str_replace('<?xml version="1.0"?>', '', $xml);
        
        return $xml;
    }
}