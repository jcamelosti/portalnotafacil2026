<?php

namespace JCamelo\NfseNacionalLib\XML\Signer;

use App\Models\Certificado;
use App\Models\Empresa;
use DOMDocument;
use Illuminate\Support\Facades\Log;
use JCamelo\NfseNacionalLib\Manager\CertificateManager;
use NFePHP\Common\Certificado as CommonCertificado;

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
   
        //$assinatura = $tools->sign($xml, $tag, '', 'DPS'); 
        //$assinatura = $xml = $tools->sign($xml, 'LoteDps', 'Id', 'EnviarLoteDpsSincronoEnvio');

        //meu método de assinatura
        $assinatura = $this->assinarRpsRepetidamenteApi($xml, $tag, $empresa->cpf_cnpj);

        $dom->loadXML($assinatura);
        $xmlFormatado = $dom->saveXML();
        
        Log::info($xmlFormatado);
        
        return $xmlFormatado;
    }

    public function assinarRpsRepetidamenteApi($xml, $tag = '', $prestadorCpfCnpj)
    {
        $oCert = new CommonCertificado();    
       
        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_LOCAL");
        }else{
            $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_PROD");
        }

        $empresaSessao = Empresa::where('cpf_cnpj', $prestadorCpfCnpj)->first();
       
        $certificadoCliente = Certificado::where('empresa_id', $empresaSessao->id)
            ->first();
        $oCert->cnpj = $empresaSessao->cpf_cnpj;

        if(getenv("AMBIENTE_PRODUCAO") == 0){
            $certificadoPath = getenv("CAMINHO_CERTIFICADO_LOCAL").$certificadoCliente->arquivo;
        }else{
            $certificadoPath = getenv("CAMINHO_CERTIFICADO_PROD").$certificadoCliente->arquivo;
        }

        $oCert->loadPfxFile(
           $certificadoPath,
           base64_decode($certificadoCliente->senha)
        );

        if($tag == ''){
            $dom = new \DomDocument;
            $dom->loadXML($xml);
            $root=$dom->documentElement; 
            $tag = $root->tagName;
        }        
        
        $s = $oCert->assinarPadraoNacional($xml, $tag);

        return $s;
    }
}