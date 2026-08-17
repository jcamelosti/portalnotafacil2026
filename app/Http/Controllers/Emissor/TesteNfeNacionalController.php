<?php

namespace App\Http\Controllers\Emissor;

use App\Http\Controllers\Controller;
use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;
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
        /*
        CONSULTA DADOS CADASTRAIS - ISSNET
        */
        
        //Cloud Fender
        $consultarDadosCadastraisDTO = $this->nfse->consultarDadosCadastrais(
            'issnet',
            361, // 🔥 empresa dinâmica - referencia para buscar certificado digital,
            '22645177000188', // 🔥 cnpj dinâmico
            '4048539' // 🔥 inscrição municipal dinâmica
        );

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
        /*$dataSN = new DPSDataDTO(
            cnpjPrestador: '22645177000188',
            imPrestador: '4048539',

            razaoTomador: '24.685.881 JOSUE CAMELO DOS SANTOS',
            cnpjTomador: '24685881000190',
            
            codigoMunicipio: '5002704',
            codigoTributacaoNacional: '171901',
            codigoServico: '105',
            descricaoServico: 'SERVIÇO SN',
            valorServico: 10.15,
            dataCompetencia: date('Y-m-d'),

            opSimpNac: 3,
            regApTribSN: 1,
            numDps: 6
        );*/

        //$simplesNacionalDPS = DPSFactory::make($dataSN);

        //funcionando normalmente
        /*$response = $this->nfse->gerarNfse(
            'issnet',
            $dataSN,
            361 // empresaId
        );*/

        //consultarUrlNfse - indisponivel em 08/05/2026
        //$this->nfse->consultarUrlNfse('issnet', 361, '22645177000188', '4048539', 1, '2026-04-20', '2026-04-30');

        //RecepcionarLoteDpsSincrono - Emitindo Normalmente
        //this->nfse->recepcionarLoteDpsSincrono('issnet', $dataSN, 361);



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
