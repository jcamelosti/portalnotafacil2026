<?php

namespace App\Http\Controllers\Emissor;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Carbon\Carbon;
use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;
use JCamelo\NfseNacionalLib\Factories\DPSFactory;
use JCamelo\NfseNacionalLib\Services\NFSeService;

class TesteNfeNacionalController extends Controller
{
    private NFSeService $nfse;

    public function __construct(NFSeService $nfse)
    {
        $this->nfse = $nfse;
    }
    
    public function teste()
    {
        $empresa = Empresa::find(361); // Substitua pelo ID da empresa que deseja testar
     
        /*
        CONSULTA DADOS CADASTRAIS - ISSNET
        */
        
        //Cloud Fender
        /*$consultarDadosCadastraisDTO = $this->nfse->consultarDadosCadastrais(
            'issnet',
            361, // 🔥 empresa dinâmica - referencia para buscar certificado digital,
            '22645177000188', // 🔥 cnpj dinâmico
            '4048539' // 🔥 inscrição municipal dinâmica
        );

        dd($consultarDadosCadastraisDTO);**/

        //Terceirize
        /*$consultarDadosCadastraisDTO = $this->nfse->consultarDadosCadastrais(
            'issnet',
            346, // 🔥 empresa dinâmica - referencia para buscar certificado digital,
            '53699630000162', // 🔥 cnpj dinâmico
            '113317' // 🔥 inscrição municipal dinâmica
        );*/

        /*$consultarDadosCadastraisDTO = $this->nfse->consultarDadosCadastrais(
            'issnet',
            182, // 🔥 empresa dinâmica - referencia para buscar certificado digital,
            '22958361000188', // 🔥 cnpj dinâmico
            '79014' // 🔥 inscrição municipal dinâmica
        );*/

        //dd($consultarDadosCadastraisDTO);
        
        //gerando xml DPS
        $dataSN = new DPSDataDTO(
            ambiente:2,
            dataEmissao: now()->format('Y-m-d\TH:i:sP'),
            serieDps: 8,
            numDps: 1,

            cnpjPrestador: '22645177000188',
            imPrestador: '4048539',

            cnpjTomador: preg_replace('/\D/', '', $dados['cnpjTomador'] ?? ''),
            razaoTomador: $dados['razaoTomador'] ?? '',
            cMunTomador: $dados['cMunTomador'] ?? '',
            cepTomador: preg_replace('/\D/', '', $dados['cepTomador'] ?? ''),
            logradouroTomador: $dados['logradouroTomador'] ?? '',
            numeroTomador: $dados['numeroTomador'] ?? '',
            complementoTomador: $dados['complementoTomador'] ?? '',
            bairroTomador: $dados['bairroTomador'] ?? '',
            cPaisTomadorExterior: $dados['cPaisTomadorExterior'] ?? '1058',
            cEndPostTomador: $dados['cEndPostTomador'] ?? '',
            xCidadeTomador: $dados['xCidadeTomador'] ?? '',

            localPrestacaoServico: $dados['localPrestacaoServico'] ?? '',

            codigoMunicipio: '5002704',            
            codigoTributacaoNacional: $dados['cTribNac'] ?? '',
            codigoServico: $dados['codigoServico'] ?? '',
            descricaoServico: $dados['txtDescServicos'] ?? '',
            valorServico:  100.00,
            dataCompetencia: $dados['dataCompetencia'] ?? now()->format('Y-m-d'),
            nbs: $dados['nbs'] ?? '',

            complemento: $dados['complemento'] ?? '',

            opSimpNac: 3,
            regApTribSN: 1,
            regEspTrib: 0,

            tribISSQN: (int) ($dados['tribISSQN'] ?? 1),
            tpRetISSQN: (int) ($dados['tpRetISSQN'] ?? 1),

            tribMunAliq:  0.0,
            tribFedCst: $dados['tribFedCst'] ?? '',
            tpRetPisCofins: (int) ($dados['tpRetPisCofins'] ?? 1),

            vRetCP: 0.0,
            vRetIRRF: 0.0,
            vRetCSLL: 0.0,

            pTotTribSN: 0.0,
            cIndOp: $dados['cIndOp'] ?? '',
            cstIbsCbs: $dados['cstIbsCbs'] ?? '',
            cClassTrib: $dados['cClassTrib'] ?? '',
        );

        /*$simplesNacionalDPS = DPSFactory::make($dataSN);
        dd($simplesNacionalDPS);*/

        //funcionando normalmente
        $response = $this->nfse->gerarNfse(
            'issnet',
            $dataSN,
            361 // empresaId
        );

        dd($response);

        
        //consultarUrlNfse - indisponivel em 08/05/2026
        //$this->nfse->consultarUrlNfse('issnet', 361, '22645177000188', '4048539', 8, '2026-05-20', '2026-05-30');

        //RecepcionarLoteDpsSincrono - Emitindo Normalmente
        //$this->nfse->recepcionarLoteDpsSincrono('issnet', $dataSN, $empresa->id);



        //Testes com Empresa do Regime Normal
        /*$dataRN = new DPSDataDTO(
            cnpjPrestador: '37268448000109',//cnpj cel
            imPrestador: '1269984',//im cel

            razaoTomador: '24.685.881 JOSUE CAMELO DOS SANTOS',
            cnpjTomador: '24685881000190',
            codigoMunicipio: '5300108',
            codigoTributacaoNacional: '171901',
            codigoServico: '105',
            descricaoServico: 'SERVIÇO NORMAL',
            valorServico: 100.08,
            dataCompetencia: date('Y-m-d'),

            opSimpNac: 1,
            regApTribSN: null,
            numDps: 1
        );*/ 
    }
}
