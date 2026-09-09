<?php

namespace App\Http\Controllers\Emissor;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;
use JCamelo\NfseNacionalLib\DTO\DPSDataSnDTO;
use JCamelo\NfseNacionalLib\Factories\DPSFactory;
use JCamelo\NfseNacionalLib\Manager\CertificateManager;
use JCamelo\NfseNacionalLib\Services\NFSeService;

class TesteNfeNacionalController extends Controller
{
    private NFSeService $nfse;

    public function __construct(NFSeService $nfse, private CertificateManager $certManager)
    {
        $this->nfse = $nfse;
    }
    
    public function teste()
    {
        $empresa = Empresa::find(361); // Substitua pelo ID da empresa que deseja testar
        /*$consultarDadosCadastraisDTO = $this->nfse->consultarDadosCadastrais(
            'issnet',
            444, // 🔥 empresa dinâmica - referencia para buscar certificado digital,
            $empresa->cpf_cnpj, // 🔥 cnpj dinâmico
            $empresa->inscricao_municipal // 🔥 inscrição municipal dinâmica
        );
        exit;

        dd($consultarDadosCadastraisDTO);*/
        
        //nfse gerarnfse notacontrol funcionando 09/09/2026
        /*$dataSN = new DPSDataSnDTO(
            ambiente: 2,
            dataEmissao: Carbon::now('America/Sao_Paulo')->format('Y-m-d\TH:i:sP'),
            serie: '8',
            numDps: 13,
            dataCompetencia: Carbon::now(
                'America/Sao_Paulo'
            )->format('Y-m-d'),
            codigoMunicipio: '5002704',
            cnpjPrestador: '22645177000188',
            imPrestador: '4048539',
            fonePrestador: '62991728787',
            emailPrestador: 'virlei79@gmail.com',
            opSimpNac: 3,//campo importante para saber qual tipo de xml deve ser gerado no factory
            regApTribSN: 1,
            regEspTrib: 0,
            cnpjTomador: '24685881000190',
            cpfTomador: null,
            razaoTomador:
               'Josue Camelo dos Santos Ferreira 01582713197',
            codigoMunicipioTomador: '5201108',
            cepTomador: '75064350',
            logradouroTomador:
               'Rua Carlinhos José Ribeiro',
            numeroTomador: '180',
            complementoTomador: 'APT 402D',
            bairroTomador:
               'Vila Jaiara Setor Leste',
            foneTomador: '6237027225',
            emailTomador:
               'contato@josuecamelo.com',
            codigoTributacaoNacional: '010101',
            codigoServicoMunicipal: '4',
            descricaoServico:
               'Manutenção de computador; limpeza, formatação & instalação - R$ 350,00 (urgente)!',
            codigoNbs: '115021000',
            codigoMunicipioPrestacao: '5002704',
            valorServico: '350.00',
            tributaIss: 1,
            tipoRetencaoIss: 1,
            aliquotaIss: '2.50',
            cstPisCofins: '00',
            tipoRetencaoPisCofins: 0,
            valorRetencaoCp: '0.12',
            valorRetencaoIrrf: '0.01',
            percentualTotalTributos: '5.00',
            finNfse: 0,
            cIndOp: '100301',
            indDest: 0,
            cstIbsCbs: '000',
            cClassTrib: '000001',
        );

        //funcionando normalmente
        $response = $this->nfse->gerarNfse(
            'issnet',
            $dataSN,
            361 // empresaId
        );

        dd($response);*/


        //string $provider, int $empresaId, string $cnpj, string $im, int $numero_nfse, string $data_inicial, string $data_final
        $response = $this->nfse->consultarUrlNfse(
            'issnet',
            $empresa->id,
            $empresa->cpf_cnpj,//cnpj            
            $empresa->inscricao_municipal, //im,
            11, //nNFSe,
            '',//dt ini
            ''//dt fim
        );

        dd($response);














        //$integrationId = 'DPS-' . $dataSN->numDps . '-' . $dataSN->serieDps;
        $payload = [
            /*
            * Discriminação dos serviços
            * XML: xDescServ
            */
            'description' => 'TESTE 1 - Api Portal Nota Fácil',

            /*
            * Totais
            */
            'total' => [
                /*
                * XML: vServ
                */
                'invoiceAmount' => 10.00,

                /*
                * XML: vLiq
                */
                'netAmount' => 10.00,

                /*
                * XML: vBC
                */
                'issBaseTax' => 10.00,

                /*
                * XML: pAliqAplic
                */
                'issRate' => 2.50,

                /*
                * XML: vISSQN
                */
                'issAmount' => 0.00,

                /*
                * Retenções federais
                *
                * O XML não possui valores de retenção,
                * portanto ficam zerados.
                */
                'irAmount' => 0.00,
                'pisAmount' => 0.00,
                'cofinsAmount' => 0.00,
                'inssAmount' => 0.00,
                'csllAmount' => 0.00,
                'othersAmount' => 0.00,
                'deductionsAmount' => 0.00,

                /*
                * Não existem retenções no XML.
                */
                'irWithheld' => false,
                'pisWithheld' => false,
                'cofinsWithheld' => false,
                'inssWithheld' => false,
                'csllWithheld' => false,
                'issWithheld' => false,

                /*
                * IBS/CBS
                *
                * XML:
                * pIBSUF       = 0.10
                * pRedAliqUF   = 0.00
                * pIBSMun      = 0.00
                * pRedAliqMun  = 0.00
                * pCBS         = 0.90
                * pRedAliqCBS  = 0.00
                */
                'ibsStateRate' => 0.10,
                'ibsStateRedRate' => 0.00,

                'ibsCityRate' => 0.00,
                'ibsCityRedRate' => 0.00,

                'cbsRate' => 0.90,
                'cbsRedRate' => 0.00,

                /*
                * XML: vBC do IBS/CBS
                */
                'ibsCbsBaseTax' => 10.00,

                /*
                * XML:
                * vIBSUF  = 0.01
                * vIBSMun = 0.00
                * vIBSTot = 0.01
                * vCBS    = 0.09
                */
                'ibsStateAmount' => 0.01,
                'ibsCityAmount' => 0.00,
                'ibsAmount' => 0.01,
                'cbsAmount' => 0.09,

                /*
                * Valor recebido pelo serviço.
                */
                'receivedAmount' => 10.00,
            ],

            /*
            * Identificador da integração.
            *
            * Recomendo que seja único por NFS-e.
            */
            'integrationId' => $integrationId,

            /*
            * XML: dhEmi
            */
            'issuedOn' => Carbon::now('America/Sao_Paulo')
                ->utc()
                ->format('Y-m-d\TH:i:s\Z'),

            /*
            * XML: dCompet
            */
            'effectiveDate' => Carbon::now('America/Sao_Paulo')
                ->utc()
                ->format('Y-m-d\TH:i:s\Z'),

            /*
            * TOMADOR
            *
            * XML: <toma>
            */
            'receiver' => [
                /*
                * XML: xNome
                */
                'name' => 'CEL ENGENHARIA LTDA',

                /*
                * XML: CNPJ
                */
                'federalTaxNumber' => '37268448000109',

                /*
                * Não informado no XML.
                */
                //'stateTaxNumber' => 'isento', //inscrição omunicipa
                //'suframaTaxNumber' => null,
                //'cityTaxNumber' => '1269984',//inscrição municipal - obrigatório

                /*
                * XML: email
                */
                //'email' => 'lazaro@cel.eng.br',

                //'phoneNumber' => null,

                /*
                * Endereço do tomador
                */
                'address' => [
                    /*
                    * XML: xLgr
                    */
                    'street' => 'ESTRADA D',

                    /*
                    * XML: xBairro
                    */
                    'district' => 'CHACARAS BOTAFOGO',

                    /*
                    * XML: CEP
                    */
                    'postalCode' => '74711120',

                    /*
                    * XML: nro
                    */
                    'number' => '88',

                    /*
                    * XML: xCpl
                    */
                    'additionalInformation' => 'QUADRACH LOTE 36',

                    /*
                    * XML: cMun
                    */
                    'city' => [
                        'name' => 'Goiânia',
                        'state' => 'GO',
                        'code' => 5208707,
                    ],

                    'country' => 'BR',
                ],
            ],

            /*
            * A documentação informa que esse campo envia
            * a NFS-e por e-mail para o tomador.
            */
            'sendEmailToCustomer' => false,

            /*
            * true = emitir imediatamente
            */
            'issue' => true,

            /*
            * RPS / DPS
            *
            * XML:
            * serie = 00008
            * nDPS  = 32
            */
            'rpsNumber' => 6,
            'rpsSeries' => '00008',

            /*
            * Códigos fiscais
            *
            * XML:
            * cTribNac = 010302
            * cTribMun = 4
            * cNBS     = 115090000
            */
            'nationalTaxationCode' => '010302',
            'cityServiceCode' => '4',
            'nbsCode' => '115090000',

            /*
            * Tributação
            *
            * XML:
            * tribISSQN = 1
            *
            * O XML indica tributação no município.
            */
            //'taxationType' => 'taxationInMunicipality',

            /*
            * XML:
            * cLocPrestacao = 5208707
            *
            * A prestação ocorreu no município.
            */
            //'taxLocation' => 'serviceProvisionMunicipality',

            /*
            * Local da prestação
            */
            'location' => [
                'code' => 5208707,
                'name' => 'Goiânia',
            ],

            /*
            * CST PIS/COFINS
            *
            * XML: <CST>08</CST>
            */
            'cstPisCofins' => '08',

            /*
            * IBS / CBS
            *
            * XML:
            * CST        = 000
            * cClassTrib = 000001
            * cIndOp     = 100301
            */
            'ibsCbs' => [
                'cst' => 0,
                'classification' => 1,
                'operationIndicatorCode' => '100301',
                'isPersonalUse' => false,
                'operationType' => 'supplyWithSubsequentPayment'
            ],
        ];

        // Define o cabeçalho para exibir como texto puro (com quebras de linha legíveis) ou HTML <pre>
        /*echo "<pre>";
        // Transforma o array em JSON formatado
        echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "</pre>";
        exit;*/

        //Emissão e Consulta
        /*$spedyService = new \App\Services\Spedy\SpedyService($empresa->spedy_api_key);
        $resposta = $spedyService->createNfse($payload);
        $consulta = $spedyService->consultarNfse($resposta['id']);
        dd($consulta);*/
    }

    public function cadastraEmpresaFocuNfe(){
        $empresa = Empresa::find(361);
        $service = new \App\Services\FocuNfe\NotaService($empresa);

        $payload = [
            'ref' => 'NFSE-TESTE-1',
            'data_emissao' => '2026-08-07T07:34:56-0300',
            'data_competencia' => '2026-08-07',
            
            'prestador' =>[
                'codigo_municipio' => 5208707,
                'codigo_municipio_emissora' => 5208707,
                //'cnpj_prestador' => '22645177000188',
                'cnpj'=> '22645177000188',
                'inscricao_municipal_prestador' => '4048539',
                'codigo_opcao_simples_nacional' => 3,//SIMPLES NACIONAL, 1 NÃO OPTANTE(REGIME NORMAL)
                'regime_especial_tributacao' => 0, //NENHUM - regime_especial_tributacao Tag XML regEspTrib
            ],
            'cnpj_tomador' => '24685881000190',
            'razao_social_tomador' => 'Josue Camelo dos Santos Ferreira 01582713197',
            'codigo_municipio_tomador' => 5208707,
            'cep_tomador' => '75.064-350',
            'logradouro_tomador' => 'Rua Carlinhos José Ribeiro',
            'numero_tomador' => 'S/N',
            'complemento_tomador' => 'Apto 402 D',
            'bairro_tomador' => 'Vila Jaiara',
            'telefone_tomador' => '62984018589',
            //'email_tomador' => 'josueprg@gmail.com',
            
            'servico' =>['codigo_municipio_prestacao' => 5208707,
            'codigo_tributacao_nacional_iss' => '010101',
            'codigo_tributacao_municipal_iss' => '4',
            'descricao_servico' => 'Teste NFSe reforma tributaria API',
            'valor_servico' => 1.0,
            'tributacao_iss' => 1,
            'tipo_retencao_iss' => 1,
            'codigo_nbs' => '115021000',
            'percentual_total_tributos_simples_nacional' => '5',],

            'codigo_indicador_operacao' => '100301',
            'ibs_cbs_situacao_tributaria' => '000',
            'ibs_cbs_classificacao_tributaria' => '000001',

            'finalidade_emissao' => 0,
            'consumidor_final' => 1,
            'indicador_destinatario' => 1,

            /*'inscricao_imobiliaria' => '000',
            'codigo_municipio_obra' => 5208707,
            'cep_obra' => '74223-042',
            'logradouro_obra' => 'Rua Fictícia',
            'numero_obra' => '1234',
            'complemento_obra' => 'ap02',
            'bairro_obra' => 'CENTRO',*/

            /*'inscricao_imobiliaria_imovel' => 456,
            'codigo_municipio_imovel' => 5208707,
            'cep_imovel' => '74223-042',
            'logradouro_imovel' => 'Rua Fictícia',
            'numero_imovel' => '1234',
            'complemento_imovel' => 'ap02',
            'bairro_imovel' => 'CENTRO',*/
        ];

        //echo "<pre>";
        // Transforma o array em JSON formatado
        //echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        //Log::info(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        //echo "</pre>";
        //exit;
        dd($service->emitirNfse($payload));
        dd($service->consultarNfse('NFSE-TESTE-02'));
    }
}
