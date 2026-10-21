<?php

namespace App\Http\Controllers\Emissor;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\NotaEmitida;
use App\Services\NotaFiscalServico\DanfsePdfService;
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
        $empresa = Empresa::find(1); // Substitua pelo ID da empresa que deseja testar
        //string $provider, int $empresaId, string $cnpj, string $im, int $numero_nfse, string $data_inicial, string $data_final
        /*$response[] = $this->nfse->consultarUrlNfse(
            'issnet',
            $empresa->id,
            $empresa->cpf_cnpj,//cnpj            
            $empresa->inscricao_municipal, //im,
            16, //nNFSe,
            '',//dt ini
            ''//dt fim
        );
        
        //Consulta do XML
        $response[] = $this->nfse->consultarXml(
            'issnet',
            $empresa->id,
            $empresa->cpf_cnpj,//cnpj            
            $empresa->inscricao_municipal, //im,
            15, //nNFSe,
            '',//dt ini
            ''//dt fim
        );


        dd([
            'ConsultaNfseUrlTest' => $response[0],
            'ConsultarNfseXmlTest' => $response[1]
        ]);*/

        //Período Máximo de Consulta 30 dias.
        /*$retorno = $this->nfse->consultarNfseServicosPrestados(
            'issnet',
            $empresa->id,
            $empresa->cpf_cnpj,//cnpj            
            $empresa->inscricao_municipal,
            null,
            '2026-08-30',//dt ini
            '2026-10-07',//dt fim
            null
        );*/

        /*$retorno = $this->nfse->consultarNfseServicosTomados(
            'issnet',
            $empresa->id,
            $empresa->cpf_cnpj,//cnpj            
            $empresa->inscricao_municipal,
            null,
            '2026-07-01',//dt ini
            '2026-07-30',//dt fim
            null
        );*/

        //Utilitarios::sendMessage("Mensagem Para Vários Usuários do ChatBot");

        $nota = NotaEmitida::where('id', 61)->first();
        
        $danfsePdfService = app(DanfsePdfService::class);
        $pdf = $danfsePdfService->renderFromXml($nota->nfse_xml);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="NFSe.pdf"',
            'Content-Transfer-Encoding' => 'binary',
            'Accept-Ranges' => 'bytes',
        ]);
    }
}
