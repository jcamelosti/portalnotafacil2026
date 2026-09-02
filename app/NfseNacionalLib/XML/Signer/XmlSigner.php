<?php

namespace JCamelo\NfseNacionalLib\XML\Signer;

use App\Models\Empresa;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use DOMDocument;
use DOMElement;
use Exception;
use Illuminate\Support\Facades\Log;
use JCamelo\NfseNacionalLib\Manager\CertificateManager;
use NFePHP\Common\Certificado;

class XmlSigner
{
    private const NAMESPACE_NFSE =
        'http://www.sped.fazenda.gov.br/nfse';

    private const NAMESPACE_XMLDSIG =
        'http://www.w3.org/2000/09/xmldsig#';

    private const C14N =
        'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';

    private const ENVELOPED =
        'http://www.w3.org/2000/09/xmldsig#enveloped-signature';

    private const SHA1 =
        'http://www.w3.org/2000/09/xmldsig#sha1';

    private const RSA_SHA1 =
        'http://www.w3.org/2000/09/xmldsig#rsa-sha1';
    
    public function __construct(
        private CertificateManager $certManager
    ) {}

public function assinarDps(
    string $xml,
    string $arquivoCertificado,
    string $senhaCertificado
): string {

    $NAMESPACE_NFSE =
        'http://www.sped.fazenda.gov.br/nfse';

    $NAMESPACE_XMLDSIG =
        'http://www.w3.org/2000/09/xmldsig#';

    $C14N =
        'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';

    $ENVELOPED =
        'http://www.w3.org/2000/09/xmldsig#enveloped-signature';


    /*
     * ============================================================
     * 1. CARREGA XML
     * ============================================================
     */

    $dom = new \DOMDocument('1.0', 'UTF-8');

    /*
     * NÃO usar LIBXML_NOBLANKS.
     *
     * Espaços/whitespace fazem parte da canonicalização.
     */
    $dom->preserveWhiteSpace = true;
    $dom->formatOutput = false;

    libxml_use_internal_errors(true);

    if (!$dom->loadXML($xml)) {

        $erros = libxml_get_errors();

        libxml_clear_errors();

        throw new \RuntimeException(
            'XML inválido: ' . print_r($erros, true)
        );
    }

    libxml_clear_errors();


    /*
     * ============================================================
     * 2. XPATH
     * ============================================================
     */

    $xpath = new \DOMXPath($dom);

    $xpath->registerNamespace(
        'nfse',
        $NAMESPACE_NFSE
    );

    /*$xpath->registerNamespace(
        'ds',
        $NAMESPACE_XMLDSIG
    );*/


    /*
     * ============================================================
     * 3. LOCALIZA DPS
     * ============================================================
     */

    /** @var \DOMElement|null $dps */
    $dps = $xpath
        ->query('//nfse:DPS')
        ->item(0);

    if (!$dps) {
        throw new \RuntimeException(
            'Elemento DPS não encontrado.'
        );
    }


    /*
     * ============================================================
     * 4. GARANTE NAMESPACE DA DPS
     * ============================================================
     */

    $dps->setAttribute(
        'xmlns',
        $NAMESPACE_NFSE
    );


    /*
     * ============================================================
     * 5. LOCALIZA infDPS
     * ============================================================
     */

    /** @var \DOMElement|null $infDps */
    $infDps = $xpath
        ->query('.//nfse:infDPS', $dps)
        ->item(0);

    if (!$infDps) {
        throw new \RuntimeException(
            'Elemento infDPS não encontrado.'
        );
    }


    /*
     * ============================================================
     * 6. ID
     * ============================================================
     */

    $dpsId = trim(
        $infDps->getAttribute('Id')
    );

    if ($dpsId === '') {
        throw new \RuntimeException(
            'O infDPS não possui atributo Id.'
        );
    }


    /*
     * ============================================================
     * 7. REMOVE ASSINATURA ANTERIOR
     * ============================================================
     */

    $assinaturas = $xpath->query(
        './ds:Signature',
        $dps
    );

    if ($assinaturas) {

        foreach ($assinaturas as $assinatura) {

            $assinatura->parentNode?->removeChild(
                $assinatura
            );
        }
    }


    /*
     * ============================================================
     * 8. CERTIFICADO A1
     * ============================================================
     */

    if (!is_file($arquivoCertificado)) {

        throw new \RuntimeException(
            'Certificado não encontrado: ' .
            $arquivoCertificado
        );
    }

    $pkcs12 = file_get_contents(
        $arquivoCertificado
    );

    if ($pkcs12 === false) {

        throw new \RuntimeException(
            'Não foi possível ler o certificado.'
        );
    }

    $certs = [];

    if (!openssl_pkcs12_read(
        $pkcs12,
        $certs,
        $senhaCertificado
    )) {

        throw new \RuntimeException(
            'Não foi possível abrir o certificado A1. ' .
            'Verifique a senha.'
        );
    }

    if (
        empty($certs['pkey']) ||
        empty($certs['cert'])
    ) {

        throw new \RuntimeException(
            'Certificado A1 inválido.'
        );
    }

    $privateKey = $certs['pkey'];
    $certificate = $certs['cert'];


    /*
     * ============================================================
     * 9. XMLDSIG
     * ============================================================
     */

    /*$dsig = new \RobRichards\XMLSecLibs\XMLSecurityDSig(
        'ds'
    );*/

    $dsig = new \RobRichards\XMLSecLibs\XMLSecurityDSig();


    /*
     * ============================================================
     * 10. C14N INCLUSIVO
     * ============================================================
     */

    $dsig->setCanonicalMethod(
        $C14N
    );


    /*
     * ============================================================
     * 11. REFERÊNCIA AO infDPS
     * ============================================================
     */

   $dsig->addReference(
    $infDps,
    \RobRichards\XMLSecLibs\XMLSecurityDSig::SHA1,
    [
        $ENVELOPED,
        $C14N,
    ],
    [
        'id_name' => 'Id',
        'overwrite' => false,
    ]
);


    /*
     * ============================================================
     * 12. CHAVE RSA-SHA1
     * ============================================================
     */

    $key = new \RobRichards\XMLSecLibs\XMLSecurityKey(
        \RobRichards\XMLSecLibs\XMLSecurityKey::RSA_SHA1,
        [
            'type' => 'private',
        ]
    );

    $key->loadKey(
        $privateKey,
        false
    );


    /*
     * ============================================================
     * 13. ASSINA E ANEXA DIRETAMENTE NA DPS
     * ============================================================
     */

    $dsig->sign(
        $key,
        $dps
    );


    /*
     * ============================================================
     * 14. NÓ DA ASSINATURA
     * ============================================================
     */

    /** @var \DOMElement|null $signature */
    $signature = $dsig->sigNode;

    if (!$signature) {

        throw new \RuntimeException(
            'A assinatura não foi criada.'
        );
    }


    /*
     * ============================================================
     * 15. Signature/@Id
     * ============================================================
     *
     * A assinatura já foi criada pelo xmlseclibs.
     *
     * Id não participa do SignedInfo.
     */

    /*$signature->setAttribute(
        'Id',
        '' . $dpsId
    );*/


    /*
     * ============================================================
     * 16. CERTIFICADO
     * ============================================================
     *
     * Somente X509Certificate.
     */

    $dsig->add509Cert(
        $certificate,
        true,
        false
    );


    /*
     * ============================================================
     * 17. GARANTE QUE NÃO EXISTE KeyValue
     * ============================================================
     */

    $keyValues = $xpath->query(
        './/ds:KeyValue',
        $signature
    );

    if ($keyValues) {

        foreach ($keyValues as $node) {

            $node->parentNode?->removeChild(
                $node
            );
        }
    }


    /*
     * ============================================================
     * 18. VALIDA ESTRUTURA FINAL
     * ============================================================
     */

    $signedInfo = $xpath
        ->query(
            './ds:SignedInfo',
            $signature
        )
        ->item(0);

    if (!$signedInfo) {

        throw new \RuntimeException(
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
        $canonicalization->getAttribute('Algorithm') !== $C14N
    ) {

        throw new \RuntimeException(
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
        $signatureMethod->getAttribute('Algorithm')
            !== \RobRichards\XMLSecLibs\XMLSecurityKey::RSA_SHA1
    ) {

        throw new \RuntimeException(
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

        throw new \RuntimeException(
            'Reference não encontrado.'
        );
    }

    if (
        $reference->getAttribute('URI')
        !== '#' . $dpsId
    ) {

        throw new \RuntimeException(
            'URI da Reference inválida.'
        );
    }


    /*
     * Transforms
     */

    $transforms = $xpath->query(
        './ds:Transforms/ds:Transform',
        $reference
    );

    if (
        !$transforms ||
        $transforms->length !== 2
    ) {

        throw new \RuntimeException(
            'A Reference deve possuir exatamente 2 Transforms.'
        );
    }

    $transform1 =
        $transforms
            ->item(0)
            ->getAttribute('Algorithm');

    $transform2 =
        $transforms
            ->item(1)
            ->getAttribute('Algorithm');

    if ($transform1 !== $ENVELOPED) {

        throw new \RuntimeException(
            'Transform enveloped-signature inválido.'
        );
    }

    if ($transform2 !== $C14N) {

        throw new \RuntimeException(
            'Transform C14N inválido.'
        );
    }


    /*
     * DigestMethod
     */

    $digestMethod = $xpath
        ->query(
            './ds:DigestMethod',
            $reference
        )
        ->item(0);

    if (
        !$digestMethod ||
        $digestMethod->getAttribute('Algorithm')
            !== \RobRichards\XMLSecLibs\XMLSecurityDSig::SHA1
    ) {

        throw new \RuntimeException(
            'DigestMethod inválido.'
        );
    }


    /*
     * X509Certificate
     */

    $x509 = $xpath
        ->query(
            './ds:KeyInfo/ds:X509Data/ds:X509Certificate',
            $signature
        )
        ->item(0);

    if (!$x509) {

        throw new \RuntimeException(
            'X509Certificate não encontrado.'
        );
    }


    /*
     * ============================================================
     * 19. RETORNA XML
     * ============================================================
     */


    $xmlFinal = $dom->saveXML();

    return $xmlFinal;
}


    /**
     * Extrai a chave privada e o certificado do A1 (.pfx/.p12).
     *
     * @return array{
     *     0:string,
     *     1:string
     * }
     */
    private function carregarCertificado(
        string $arquivo,
        string $senha
    ): array {

        if (!is_file($arquivo)) {
            throw new Exception(
                "Certificado não encontrado: {$arquivo}"
            );
        }

        $pfx = file_get_contents($arquivo);

        if ($pfx === false) {
            throw new Exception(
                'Não foi possível ler o certificado.'
            );
        }

        $certs = [];

        if (!openssl_pkcs12_read(
            $pfx,
            $certs,
            $senha
        )) {
            throw new Exception(
                'Não foi possível abrir o certificado A1. ' .
                'Verifique o arquivo e a senha.'
            );
        }

        if (
            empty($certs['pkey']) ||
            empty($certs['cert'])
        ) {
            throw new Exception(
                'O certificado A1 não contém chave privada e certificado.'
            );
        }

        return [
            $certs['pkey'],
            $certs['cert'],
        ];
    }

    public function assinar(string $xml, int $empresaId){
        // 🔥 pega certificado isolado
        $cert = $this->certManager->getCertificate($empresaId);
               
        $signedXml = $this->assinarDps(
            $xml,
            $cert['pfx'],
            $cert['password']
        );
        
        //var_dump($signedXml);
        //exit;
        //$signedXml = str_replace(':ds', '', $signedXml);
        return $signedXml;
    }

    /**
     * Valida a estrutura da assinatura conforme o padrão
     * exigido pela NFS-e Nacional.
     */
    private function validarAssinaturaGerada(
        DOMElement $signature,
        string $dpsId,
        \DOMXPath $xpath
    ): void {

        /*
         * Signature/@Id
         */

        if (!$signature->hasAttribute('Id')) {
            throw new RuntimeException(
                'Signature/@Id não foi informado.'
            );
        }


        /*
         * SignedInfo
         */

        $signedInfo = $xpath
            ->query('./ds:SignedInfo', $signature)
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

        if (!$canonicalization) {
            throw new RuntimeException(
                'CanonicalizationMethod não encontrado.'
            );
        }

        $algoritmoC14N =
            $canonicalization->getAttribute(
                'Algorithm'
            );

        if ($algoritmoC14N !== self::C14N) {
            throw new RuntimeException(
                'CanonicalizationMethod inválido: ' .
                $algoritmoC14N
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

        if (!$signatureMethod) {
            throw new RuntimeException(
                'SignatureMethod não encontrado.'
            );
        }

        if (
            $signatureMethod->getAttribute('Algorithm')
            !== self::RSA_SHA1
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


        /*
         * URI
         */

        $uri = $reference->getAttribute(
            'URI'
        );

        if ($uri !== '#' . $dpsId) {
            throw new RuntimeException(
                'URI da assinatura inválida: ' .
                $uri
            );
        }


        /*
         * DigestMethod
         */

        $digestMethod = $xpath
            ->query(
                './ds:DigestMethod',
                $reference
            )
            ->item(0);

        if (!$digestMethod) {
            throw new RuntimeException(
                'DigestMethod não encontrado.'
            );
        }

        if (
            $digestMethod->getAttribute('Algorithm')
            !== self::SHA1
        ) {
            throw new RuntimeException(
                'DigestMethod inválido.'
            );
        }


        /*
         * Transforms
         */

        $transforms = $xpath->query(
            './ds:Transforms/ds:Transform',
            $reference
        );

        if (!$transforms || $transforms->length !== 2) {
            throw new RuntimeException(
                'A referência deve possuir exatamente 2 transforms.'
            );
        }

        $transform1 =
            $transforms->item(0)
                ->getAttribute('Algorithm');

        $transform2 =
            $transforms->item(1)
                ->getAttribute('Algorithm');


        /*
         * Primeiro transform
         */

        if ($transform1 !== self::ENVELOPED) {
            throw new RuntimeException(
                'Primeiro Transform inválido: ' .
                $transform1
            );
        }


        /*
         * Segundo transform
         */

        if ($transform2 !== self::C14N) {
            throw new RuntimeException(
                'Segundo Transform inválido: ' .
                $transform2
            );
        }


        /*
         * X509Certificate
         */

        $certificado = $xpath
            ->query(
                './ds:KeyInfo/ds:X509Data/ds:X509Certificate',
                $signature
            )
            ->item(0);

        if (!$certificado) {
            throw new RuntimeException(
                'X509Certificate não encontrado.'
            );
        }


        /*
         * Não pode existir KeyValue
         */

        if (
            $xpath->query(
                './/ds:KeyValue',
                $signature
            )->length > 0
        ) {
            throw new RuntimeException(
                'KeyValue não pode existir na assinatura.'
            );
        }


        /*
         * Não pode existir RSAKeyValue
         */

        if (
            $xpath->query(
                './/ds:RSAKeyValue',
                $signature
            )->length > 0
        ) {
            throw new RuntimeException(
                'RSAKeyValue não pode existir na assinatura.'
            );
        }
    }

    public function assinarRpsRepetidamenteApi($xml, $tag = '', $empresaId)
    {
        // 🔥 pega certificado isolado
        $cert = $this->certManager->getCertificate($empresaId);
        $oCert = new Certificado();        
        $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_LOCAL");
        $empresa = Empresa::where('id', $empresaId)->first();
        $oCert->cnpj = $empresa->cpf_cnpj;
        $certificadoPath = $cert['pfx'];
        $oCert->loadPfxFile(
           $certificadoPath,
           $cert['password']
        );

        if($tag == ''){
            $dom = new \DomDocument;
            $dom->loadXML($xml);
            $root=$dom->documentElement; 
            $tag = $root->tagName;
        }        
        
        $s = $oCert->signXMLMod2($xml, $tag);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($s);
        $xmlFormatado = $dom->saveXML();
        Log::info($xmlFormatado);
        return $xmlFormatado;
    }

    public function assinarNovo($xml, $tag = '', $empresaId){
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
        $dom->loadXML($tools->sign($xml, 'infDPS', '', 'DPS'));
        $xmlFormatado = $dom->saveXML();
        return $xmlFormatado;
    }
}