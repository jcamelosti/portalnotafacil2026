<?php

namespace JCamelo\NfseNacionalLib\XML\Signer;

use JCamelo\NfseNacionalLib\Manager\CertificateManager;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class XmlSigner
{
    public function __construct(
        private CertificateManager $certManager
    ) {}

    public function sign(string $xml, int $empresaId): string
    {
        // 🔥 pega certificado isolado
        $cert = $this->certManager->getCertificate($empresaId);

        return $this->signXml(
            $xml,
            $cert['cert'],
            $cert['password']
        );
    }

    private function signXml(string $xml, string $certPath, string $password): string
    {
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;
        $doc->loadXML($xml);

        // 🔥 pega o nó correto
        $infDPS = $doc->getElementsByTagName('infDPS')->item(0);

        if (!$infDPS) {
            throw new \Exception("Tag infDPS não encontrada");
        }

        // 🔥 pega ID
        $id = $infDPS->getAttribute('Id');

        if (!$id) {
            throw new \Exception("Atributo Id não encontrado");
        }

        // 🔥 IMPORTANTÍSSIMO
        $infDPS->setIdAttribute('Id', true);

        // 🔐 cria assinatura
        $objDSig = new XMLSecurityDSig();
        $objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);

        // 🔥 referência correta
        $objDSig->addReference(
            $infDPS,
            XMLSecurityDSig::SHA256,
            [
                'http://www.w3.org/2000/09/xmldsig#enveloped-signature',
                XMLSecurityDSig::EXC_C14N
            ],
            ['uri' => '#' . $id]
        );

        // 🔐 chave privada
        $objKey = new XMLSecurityKey(
            XMLSecurityKey::RSA_SHA256,
            ['type' => 'private']
        );

        $objKey->loadKey($certPath, true, false, $password);

        // 🔥 assina
        $objDSig->sign($objKey);

        // 🔥 adiciona certificado (OBRIGATÓRIO)
        $objDSig->add509Cert(
            file_get_contents($certPath),
            true,
            false,
            ['subjectName' => true]
        );

        // 🔥 insere no nó correto
        $objDSig->appendSignature($infDPS);

        return $doc->saveXML();
    }

    public function assinarXml(string $xml, int $empresaId, string $tag = ''): string
    {
        // 🔥 pega certificado isolado
        $cert = $this->certManager->getCertificate($empresaId);
        
        if($tag == ''){
            $dom = new \DomDocument;
            $dom->loadXML($xml);
            $root=$dom->documentElement; 
            $tag = $root->tagName;
        }        
        
        $xml_assinado = $cert['refClassCertificado']->assinarPadraoNacional($xml, $tag);
        
        $dom = new \DOMDocument;
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($xml_assinado);
        $dom->formatOutput = TRUE;
        //var_dump($dom->saveXML());
        //exit;
        return $dom->saveXml();
    }
}