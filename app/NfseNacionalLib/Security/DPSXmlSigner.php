<?php

namespace JCamelo\NfseNacionalLib\Security;


use DOMDocument;
use DOMElement;
use DOMXPath;
use RuntimeException;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class DPSXmlSigner
{
    private const NS_NFSE =
        'http://www.sped.fazenda.gov.br/nfse';

    private const NS_DS =
        'http://www.w3.org/2000/09/xmldsig#';

    private const C14N =
        'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';

    private const ENVELOPED =
        'http://www.w3.org/2000/09/xmldsig#enveloped-signature';

    public function sign(
        string $xml,
        string $certificado,
        string $senha
    ): string {
        /*
         * ---------------------------------------------------------
         * 1. DOM
         * ---------------------------------------------------------
         */
        $dom = new DOMDocument(
            '1.0',
            'UTF-8'
        );

        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        libxml_use_internal_errors(true);

        if (!$dom->loadXML(
            $xml,
            LIBXML_NOBLANKS
        )) {
            $errors = libxml_get_errors();

            libxml_clear_errors();

            throw new RuntimeException(
                'XML inválido: ' .
                print_r($errors, true)
            );
        }

        libxml_clear_errors();

        /*
         * ---------------------------------------------------------
         * 2. XPath
         * ---------------------------------------------------------
         */
        $xpath = new DOMXPath($dom);

        $xpath->registerNamespace(
            'nfse',
            self::NS_NFSE
        );

        $xpath->registerNamespace(
            'ds',
            self::NS_DS
        );

        /*
         * ---------------------------------------------------------
         * 3. Localiza infDPS
         * ---------------------------------------------------------
         */
        $infDps = $xpath
            ->query('//nfse:infDPS')
            ->item(0);

        if (!$infDps instanceof DOMElement) {
            throw new RuntimeException(
                'infDPS não encontrado.'
            );
        }

        /*
         * ---------------------------------------------------------
         * 4. Id
         * ---------------------------------------------------------
         */
        $dpsId = trim(
            $infDps->getAttribute('Id')
        );

        if ($dpsId === '') {
            throw new RuntimeException(
                'infDPS não possui Id.'
            );
        }

        /*
         * ---------------------------------------------------------
         * 5. Localiza DPS
         * ---------------------------------------------------------
         */
        $dps = $xpath
            ->query('//nfse:DPS')
            ->item(0);

        if (!$dps instanceof DOMElement) {
            throw new RuntimeException(
                'DPS não encontrada.'
            );
        }

        /*
         * ---------------------------------------------------------
         * 6. Remove assinatura anterior
         * ---------------------------------------------------------
         */
        $assinaturas = $xpath->query(
            './ds:Signature',
            $dps
        );

        foreach ($assinaturas as $assinatura) {
            $assinatura->parentNode?->removeChild(
                $assinatura
            );
        }

        /*
         * ---------------------------------------------------------
         * 7. Carrega A1
         * ---------------------------------------------------------
         */
        if (!is_file($certificado)) {
            throw new RuntimeException(
                'Certificado não encontrado: ' .
                $certificado
            );
        }

        $pkcs12 = file_get_contents(
            $certificado
        );

        if ($pkcs12 === false) {
            throw new RuntimeException(
                'Não foi possível ler o certificado.'
            );
        }

        $certs = [];

        if (!openssl_pkcs12_read(
            $pkcs12,
            $certs,
            $senha
        )) {
            throw new RuntimeException(
                'Não foi possível abrir o A1. ' .
                'Verifique a senha.'
            );
        }

        if (
            empty($certs['pkey']) ||
            empty($certs['cert'])
        ) {
            throw new RuntimeException(
                'Certificado A1 inválido.'
            );
        }

        /*
         * ---------------------------------------------------------
         * 8. XMLDSig
         * ---------------------------------------------------------
         */
        $dsig = new XMLSecurityDSig('ds');

        /*
         * C14N 1.0
         */
        $dsig->setCanonicalMethod(
            self::C14N
        );

        /*
         * ---------------------------------------------------------
         * 9. Reference
         *
         * IMPORTANTE:
         *
         * Referenciamos o infDPS.
         * NÃO criamos outro Id.
         * ---------------------------------------------------------
         */
        $dsig->addReference(
            $infDps,
            XMLSecurityDSig::SHA1,
            [
                self::ENVELOPED,
                self::C14N,
            ],
            [
                'id_name' => 'Id',
                'overwrite' => false,
            ]
        );

        /*
         * ---------------------------------------------------------
         * 10. RSA-SHA1
         * ---------------------------------------------------------
         */
        $key = new XMLSecurityKey(
            XMLSecurityKey::RSA_SHA1,
            [
                'type' => 'private',
            ]
        );

        $key->loadKey(
            $certs['pkey'],
            false
        );

        /*
         * ---------------------------------------------------------
         * 11. Assina
         *
         * IMPORTANTE:
         *
         * A Signature deve ser anexada à DPS.
         * ---------------------------------------------------------
         */
        $dsig->sign(
            $key,
            $dps
        );

        /*
         * ---------------------------------------------------------
         * 12. Certificado
         * ---------------------------------------------------------
         */
        $dsig->add509Cert(
            $certs['cert'],
            true,
            false
        );

        /*
         * ---------------------------------------------------------
         * 13. Recupera Signature
         * ---------------------------------------------------------
         */
        $signature = $dsig->sigNode;

        if (!$signature instanceof DOMElement) {
            throw new RuntimeException(
                'Signature não foi criada.'
            );
        }

        /*
         * ---------------------------------------------------------
         * 14. Validação da assinatura
         * ---------------------------------------------------------
         */
        $this->validateSignature(
            $xpath,
            $signature,
            $dpsId
        );

        /*
         * ---------------------------------------------------------
         * 15. XML FINAL
         * ---------------------------------------------------------
         */
        return $dom->saveXML();
    }

    private function validateSignature(
        DOMXPath $xpath,
        DOMElement $signature,
        string $dpsId
    ): void {
        /*
         * SignedInfo
         */
        $signedInfo = $xpath
            ->query(
                './ds:SignedInfo',
                $signature
            )
            ->item(0);

        if (!$signedInfo) {
            throw new RuntimeException(
                'SignedInfo não encontrado.'
            );
        }

        /*
         * CanonicalizationMethod
         */
        $canonicalization = $xpath
            ->query(
                './ds:CanonicalizationMethod',
                $signedInfo
            )
            ->item(0);

        if (
            !$canonicalization ||
            $canonicalization->getAttribute(
                'Algorithm'
            ) !== self::C14N
        ) {
            throw new RuntimeException(
                'CanonicalizationMethod inválido.'
            );
        }

        /*
         * SignatureMethod
         */
        $signatureMethod = $xpath
            ->query(
                './ds:SignatureMethod',
                $signedInfo
            )
            ->item(0);

        if (
            !$signatureMethod ||
            $signatureMethod->getAttribute(
                'Algorithm'
            ) !== XMLSecurityKey::RSA_SHA1
        ) {
            throw new RuntimeException(
                'SignatureMethod inválido.'
            );
        }

        /*
         * Reference
         */
        $reference = $xpath
            ->query(
                './ds:Reference',
                $signedInfo
            )
            ->item(0);

        if (!$reference) {
            throw new RuntimeException(
                'Reference não encontrado.'
            );
        }

        if (
            $reference->getAttribute('URI')
            !== '#' . $dpsId
        ) {
            throw new RuntimeException(
                'Reference URI inválida.'
            );
        }

        /*
         * Transforms
         */
        $transforms = $xpath
            ->query(
                './ds:Transforms/ds:Transform',
                $reference
            );

        if (
            !$transforms ||
            $transforms->length !== 2
        ) {
            throw new RuntimeException(
                'A Reference deve possuir exatamente ' .
                '2 Transforms.'
            );
        }

        if (
            $transforms->item(0)
                ->getAttribute('Algorithm')
            !== self::ENVELOPED
        ) {
            throw new RuntimeException(
                'Transform enveloped-signature inválido.'
            );
        }

        if (
            $transforms->item(1)
                ->getAttribute('Algorithm')
            !== self::C14N
        ) {
            throw new RuntimeException(
                'Transform C14N inválido.'
            );
        }

        /*
         * Digest
         */
        $digest = $xpath
            ->query(
                './ds:DigestMethod',
                $reference
            )
            ->item(0);

        if (
            !$digest ||
            $digest->getAttribute('Algorithm')
            !== XMLSecurityDSig::SHA1
        ) {
            throw new RuntimeException(
                'DigestMethod inválido.'
            );
        }

        /*
         * X509
         */
        $x509 = $xpath
            ->query(
                './ds:KeyInfo/ds:X509Data/ds:X509Certificate',
                $signature
            )
            ->item(0);

        if (!$x509) {
            throw new RuntimeException(
                'X509Certificate não encontrado.'
            );
        }
    }
}