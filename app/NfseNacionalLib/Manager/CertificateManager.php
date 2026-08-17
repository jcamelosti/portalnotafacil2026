<?php

namespace JCamelo\NfseNacionalLib\Manager;

use App\Models\Empresa;
use App\Models\Certificado as CertificadoModel;

class CertificateManager
{
    public function getCertificate(int $empresaId): array
    {
        $empresa = Empresa::findOrFail($empresaId);

        $cert = CertificadoModel::where('empresa_id', $empresaId)->firstOrFail();

        $password = base64_decode($cert->senha);

        //$path = getenv("CAMINHO_CERTIFICADO_LOCAL") . $cert->arquivo;
        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $path = getenv("CAMINHO_CERTIFICADO_LOCAL") . $cert->arquivo;
        }else{
            $path = getenv("CAMINHO_CERTIFICADO_PROD") . $cert->arquivo;
        }

        $oCert = new \NFePHP\Common\Certificado();
        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_LOCAL");
        }else{
            $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_PROD");
        }
        $oCert->cnpj = $empresa->cpf_cnpj;

        $oCert->loadPfxFile($path, $password);

        $pem = $oCert->pathCerts . $empresa->cpf_cnpj . "_certKEY.pem";

        return [
            'cert' => $pem,
            'password' => $password,
            'refClassCertificado' => $oCert
        ];
    }
}