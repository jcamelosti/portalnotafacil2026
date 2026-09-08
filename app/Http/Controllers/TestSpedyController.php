<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Utilitarios\Utilitarios;
use Illuminate\Http\Request;
use JCamelo\NfseNacionalLib\Manager\CertificateManager;

class TestSpedyController extends Controller
{
    public function __construct(
        private CertificateManager $certManager
    ) {}

    public function index(){
        $empresa = Empresa::find(361);
        $spedyService = app(\App\Services\Spedy\SpedyService::class);
        //dd($spedyService->getCompanyList());
        //dd($spedyService->getCompanyById($empresa->spedy_id));
        

        /*$certificado = $empresa->certificado->first();
        $certificadoSpedy = $spedyService->verificarCertificado($empresa->spedy_id);

        if($certificado && !isset($certificadoSpedy[0]['id'])){
            $cd = $this->certManager->getCertificate($empresa->id);
            $certificadoUpload = $spedyService->uploadCertificate(
                $empresa->spedy_id,
                $cd['pfx'],
                $cd['password']
            );

            dd($certificadoUpload);
        }*/
        //setConfiguration
        /*$dados =[
            'serviceInvoice' => [
                'series' => $empresa->serie_dps,
                'issueType' => 'website',
                'environmentType'=> $empresa->ambiente_emissao === 'HOMOLOGACAO' ? 'development': 'production',
                'nextNumber' => 1 //número do próximo rps/dps
            ]
        ];
        $spedyService->setConfigurations($empresa->spedy_id, $dados);

        $confs = $spedyService->getConfigurations($empresa->spedy_id);
        dd($confs);*/
    }
}
