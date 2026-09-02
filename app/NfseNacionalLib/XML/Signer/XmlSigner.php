<?php

namespace JCamelo\NfseNacionalLib\XML\Signer;

use App\Models\Empresa;
use DOMDocument;
use Illuminate\Support\Facades\Log;
use JCamelo\NfseNacionalLib\Manager\CertificateManager;


class XmlSigner
{
    public function __construct(
        private CertificateManager $certManager
    ) {}

    public function assinar($xml, $tag = '', $empresaId){
        $cert = $this->certManager->getCertificate($empresaId);
        $empresa = Empresa::where('id', $empresaId)->first();
        $certificadoPath = $cert['pfx'];

        $config = new \stdClass();
        $config->tpamb = $empresa->ambiente_emissao === 'HOMOLOGACAO' ? 2 : 1;
        $configJson = json_encode($config);

        $content = file_get_contents($certificadoPath);
                
        $cert = \NFePHP\Common\Certificate::readPfx($content, $cert['password']);
        $tools = new \Hadder\NfseNacional\Tools($configJson, $cert);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
   
        $assinatura = $tools->sign($xml, $tag, '', 'DPS'); 
        //$assinatura = $xml = $tools->sign(            $xml,            'LoteDps',            'Id',            'EnviarLoteDpsSincronoEnvio'        );

        $dom->loadXML($assinatura);
        $xmlFormatado = $dom->saveXML();
        
        Log::info($xmlFormatado);
        
        return $xmlFormatado;
    }
}