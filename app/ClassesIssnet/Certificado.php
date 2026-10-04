<?php

namespace NFePHP\Common;

use Exception;

class Certificado{
    /**
     * Path para o diretorio onde o arquivo pfx está localizado
     *
     * @var string
     */
    public $pathCerts = '';
    
    /**
     * Path para o arquivo pfx (certificado digital em formato de transporte)
     *
     * @var string
     */
    public $pfxFileName = '';
    
    /**
     * Conteudo do arquivo pfx
     *
     * @var string
     */
    public $pfxCert = '';
    
    /**
     * Numero do CNPJ do emitente
     *
     * @var string
     */
    public $cnpj = '';
    
    /**
     * String que contêm a chave publica em formato PEM
     *
     * @var string
     */
    public $pubKey = '';
    
    /**
     * String quem contêm a chave privada em formato PEM
     *
     * @var string
     */
    public $priKey = '';
    
    /**
     * String que conten a combinação da chave publica e privada em formato PEM
     * e a cadeida completa de certificação caso exista
     *
     * @var string
     */
    public $certKey = '';
    
    /**
     * Flag para ignorar testes de validade do certificado
     * isso é usado apenas para fins de testes
     *
     * @var boolean
     */
    public $ignoreValidCert = false;
    
    /**
     * Path para a chave publica em arquivo
     *
     * @var string
     */
    public $pubKeyFile = '';
    
    /**
     * Path para a chave privada em arquivo
     *
     * @var string
     */
    public $priKeyFile = '';
    
    /**
     * Path para o certificado em arquivo
     *
     * @var string
     */
    public $certKeyFile = '';
    
    /**
     * Timestamp da data de validade do certificado
     *
     * @var float
     */
    public $expireTimestamp = 0;
    
    /**
     * Mensagem de erro da classe
     *
     * @var string
     */
    public $error = '';
    
    /**
     * Id do docimento sendo assinado
     *
     * @var string
     */
    public $docId = '';

    public function loadPfxFile(
        $pathPfx = '',
        $password = '',
        $createFiles = true,
        $ignoreValidity = false,
        $ignoreOwner = false
    ) {
        if (! is_file($pathPfx)) {
            throw new Exception(
                "O nome do arquivo PFX deve ser passado. Não foi localizado o arquivo [$pathPfx]."
            );
        }
        $this->pfxCert = file_get_contents($pathPfx);
        return $this->loadPfx($this->pfxCert, $password, $createFiles, $ignoreValidity, $ignoreOwner);
    }

    /**
     * loadPfx
     * Carrega um novo certificado no formato PFX
     * Isso deverá ocorrer a cada atualização do certificado digital, ou seja,
     * pelo menos uma vez por ano, uma vez que a validade do certificado
     * é anual.
     * Será verificado também se o certificado pertence realmente ao CNPJ
     * Essa verificação checa apenas se o certificado pertence a matriz ou filial
     * comparando apenas os primeiros 8 digitos do CNPJ, dessa forma ambas a
     * matriz e as filiais poderão usar o mesmo certificado indicado na instanciação
     * da classe, se não for um erro irá ocorrer e
     * o certificado não será convertido para o formato PEM.
     * Em caso de erros, será retornado false e o motivo será indicado no
     * parâmetro error da classe.
     * Os certificados serão armazenados como <CNPJ>-<tipo>.pem
     *
     * @param  string  $pfxContent     arquivo PFX
     * @param  string  $password       Senha de acesso ao certificado PFX
     * @param  boolean $createFiles    se true irá criar os arquivos pem das chaves digitais, caso contrario não
     * @param  bool    $ignoreValidity
     * @param  bool    $ignoreOwner
     * @return bool
     */
    public function loadPfx(
        $pfxContent = '',
        $password = '',
        $createFiles = true,
        $ignoreValidity = false,
        $ignoreOwner = false
    ) {
        if ($password == '') {
            throw new Exception(
                "A senha de acesso para o certificado pfx não pode ser vazia."
            );
        }
        //carrega os certificados e chaves para um array denominado $x509certdata
        $x509certdata = array();
        if (!openssl_pkcs12_read($pfxContent, $x509certdata, $password)) {
            throw new Exception(
                "O certificado não pode ser lido!! Senha errada ou arquivo corrompido ou formato inválido!!"
            );
        }
        $this->pfxCert = $pfxContent;
        if (!$ignoreValidity) {
            //verifica sua data de validade
            /*if (! $this->zValidCerts($x509certdata['cert'])) {
                throw new Exception\RuntimeException($this->error);
            }*/
        }
        if (!$ignoreOwner) {
            /*$cnpjCert = Asn::getCNPJCert($x509certdata['cert']);
            if (substr($this->cnpj, 0, 8) != substr($cnpjCert, 0, 8)) {
                throw new Exception\InvalidArgumentException(
                    "O Certificado fornecido pertence a outro CNPJ!!"
                );
            }*/
        }
        //monta o path completo com o nome da chave privada
        $this->priKeyFile = $this->pathCerts.$this->cnpj.'_priKEY.pem';
        //monta o path completo com o nome da chave publica
        $this->pubKeyFile =  $this->pathCerts.$this->cnpj.'_pubKEY.pem';
        //monta o path completo com o nome do certificado (chave publica e privada) em formato pem
        $this->certKeyFile = $this->pathCerts.$this->cnpj.'_certKEY.pem';
        //$this->zRemovePemFiles();
        if ($createFiles) {
            //$this->zSavePemFiles($x509certdata);

            file_put_contents($this->pubKeyFile, $x509certdata['cert']);
            file_put_contents($this->certKeyFile, $x509certdata['pkey']."\r\n".$x509certdata['cert']);
        }
        $this->pubKey=$x509certdata['cert'];
        $this->priKey=$x509certdata['pkey'];
        $this->certKey=$x509certdata['pkey']."\r\n".$x509certdata['cert'];
        return true;
    }

    public function signXML($docxml, $tagid = '')
    {
        /*
        //caso não tenha as chaves cai fora
        if ($this->pubKey == '' || $this->priKey == '') {
            $msg = "As chaves não estão disponíveis.";
            throw new Exception\InvalidArgumentException($msg);
        }
        //caso não seja informada a tag a ser assinada cai fora
        if ($tagid == '') {
            $msg = "A tag a ser assinada deve ser indicada.";
            throw new Exception\InvalidArgumentException($msg);
        }*/
        //carrega a chave privada no openssl
        $objSSLPriKey = openssl_get_privatekey($this->priKey);
        if ($objSSLPriKey === false) {
            $msg = "Houve erro no carregamento da chave privada.";
            $this->zGetOpenSSLError($msg);
            //while ($erro = openssl_error_string()) {
            //    $msg .= $erro . "\n";
            //}
            //throw new Exception\RuntimeException($msg);
        }
        $xml = $docxml;
        if (is_file($docxml)) {
            $xml = file_get_contents($docxml);
        }
        //remove sujeiras do xml
        $order = array("\r\n", "\n", "\r", "\t");
        $xml = str_replace($order, '', $xml);
        $xmldoc =  new \DOMDocument();
        $xmldoc->loadXML($xml);
        //coloca o node raiz em uma variável
        $root = $xmldoc->documentElement;
        
        if($tagid != ''){
           $root = $xmldoc->getElementsByTagName($tagid)->item(0);
        }

        //extrair a tag com os dados a serem assinados
        $node = $xmldoc->getElementsByTagName($tagid)->item(0);
        if (!isset($node)) {
            throw new \Exception(
                "A tag < $tagid > não existe no XML!!"
            );
        }
        //$this->docId = $node->getAttribute('Id');
        $xmlResp = $xml;
        if (! $this->zSignatureExists($xmldoc)) {
            //executa a assinatura
            $xmlResp = $this->zSignXML($xmldoc, $root, $node, $objSSLPriKey);
        }
        //libera a chave privada
        //openssl_free_key($objSSLPriKey);
        return $xmlResp;
    }

    public function efetuarAssinaturaXML($docxml, $tagid = '')
    {
        $objSSLPriKey = openssl_get_privatekey($this->priKey);

        if ($objSSLPriKey === false) {
            $msg = "Houve erro no carregamento da chave privada.";
            $this->zGetOpenSSLError($msg);
            throw new \Exception($msg);
        }

        $xml = $docxml;

        if (is_file($docxml)) {
            $xml = file_get_contents($docxml);
        }

        // Remove quebras e tabs desnecessários
        $order = array("\r\n", "\n", "\r", "\t");
        $xml = str_replace($order, '', $xml);

        $xmldoc = new \DOMDocument();
        $xmldoc->preserveWhiteSpace = false;
        $xmldoc->formatOutput = true;

        if (!$xmldoc->loadXML($xml)) {
            throw new \Exception("Não foi possível carregar o XML.");
        }

        /*
        * ---------------------------------------------------------
        * NODE QUE SERÁ ASSINADO
        * ---------------------------------------------------------
        */
        $node = $xmldoc->getElementsByTagName($tagid)->item(0);

        if (!$node) {
            throw new \Exception(
                "A tag <{$tagid}> não existe no XML!"
            );
        }

        /*
        * ---------------------------------------------------------
        * ELEMENTO PAI DO NODE ASSINADO
        *
        * Para:
        *
        * <DPS>
        *     <infDPS>...</infDPS>
        * </DPS>
        *
        * queremos que Signature seja irmã de infDPS.
        * Portanto:
        *
        * $node       = infDPS
        * $root       = DPS
        * ---------------------------------------------------------
        */
        $root = $node->parentNode;

        if (!$root) {
            throw new \Exception(
                "Não foi possível localizar o elemento pai de <{$tagid}>."
            );
        }

        /*
        * Executa a assinatura
        */
        $xmlResp = $this->zSignXMLTeste(
            $xmldoc,
            $root,
            $node,
            $objSSLPriKey
        );

        return $xmlResp;
    }

    private function zSignXMLTeste(
    $xmldoc,
    $root,
    $node,
    $objSSLPriKey
) {
    $nsDSIG = 'http://www.w3.org/2000/09/xmldsig#';

    $nsCannonMethod =
        'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';

    $nsSignatureMethod =
        'http://www.w3.org/2000/09/xmldsig#rsa-sha1';

    $nsTransformMethod1 =
        'http://www.w3.org/2000/09/xmldsig#enveloped-signature';

    $nsTransformMethod2 =
        'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';

    $nsDigestMethod =
        'http://www.w3.org/2000/09/xmldsig#sha1';

    /*
     * Prefixo que queremos explicitamente:
     *
     * ns2:Signature
     * ns2:SignedInfo
     * ns2:Reference
     * ...
     */
    $prefix = 'ns2';

    /*
     * Canonicalização usada pelo Signer.
     */
    $canonical = $this->canonical ?? [
        true,
        false,
        null,
        null
    ];

    /*
     * ==========================================================
     * ID DO ELEMENTO ASSINADO
     * ==========================================================
     */
    $idSigned = trim(
        $node->getAttribute('Id')
    );

    /*
     * ==========================================================
     * DIGEST
     * ==========================================================
     */
    $dados = $node->C14N(
        $canonical[0],
        $canonical[1],
        $canonical[2],
        $canonical[3]
    );

    $hashValue = hash(
        'sha1',
        $dados,
        true
    );

    $digValue = base64_encode($hashValue);

    /*
     * ==========================================================
     * SIGNATURE
     * ==========================================================
     *
     * IMPORTANTE:
     *
     * createElementNS(
     *     namespace,
     *     'ns2:Signature'
     * )
     *
     * cria explicitamente o prefixo ns2.
     */
    $signatureNode = $xmldoc->createElementNS(
        $nsDSIG,
        $prefix . ':Signature'
    );

    /*
     * Declara explicitamente:
     *
     * xmlns:ns2="http://www.w3.org/2000/09/xmldsig#"
     */
    $signatureNode->setAttributeNS(
        'http://www.w3.org/2000/xmlns/',
        'xmlns:' . $prefix,
        $nsDSIG
    );

    /*
     * Signature fica dentro da raiz DPS.
     */
    $root->appendChild(
        $signatureNode
    );

    /*
     * ==========================================================
     * SIGNED INFO
     * ==========================================================
     */
    $signedInfoNode = $xmldoc->createElementNS(
        $nsDSIG,
        $prefix . ':SignedInfo'
    );

    $signatureNode->appendChild(
        $signedInfoNode
    );

    /*
     * ==========================================================
     * CANONICALIZATION METHOD
     * ==========================================================
     */
    $canonicalNode = $xmldoc->createElementNS(
        $nsDSIG,
        $prefix . ':CanonicalizationMethod'
    );

    $canonicalNode->setAttribute(
        'Algorithm',
        $nsCannonMethod
    );

    $signedInfoNode->appendChild(
        $canonicalNode
    );

    /*
     * ==========================================================
     * SIGNATURE METHOD
     * ==========================================================
     */
    $signatureMethodNode = $xmldoc->createElementNS(
        $nsDSIG,
        $prefix . ':SignatureMethod'
    );

    $signatureMethodNode->setAttribute(
        'Algorithm',
        $nsSignatureMethod
    );

    $signedInfoNode->appendChild(
        $signatureMethodNode
    );

    /*
     * ==========================================================
     * REFERENCE
     * ==========================================================
     */
    $referenceNode = $xmldoc->createElementNS(
        $nsDSIG,
        $prefix . ':Reference'
    );

    $referenceNode->setAttribute(
        'URI',
        '#' . $idSigned
    );

    $signedInfoNode->appendChild(
        $referenceNode
    );

    /*
     * ==========================================================
     * TRANSFORMS
     * ==========================================================
     */
    $transformsNode = $xmldoc->createElementNS(
        $nsDSIG,
        $prefix . ':Transforms'
    );

    $referenceNode->appendChild(
        $transformsNode
    );

    /*
     * Transform enveloped
     */
    $transfNode1 = $xmldoc->createElementNS(
        $nsDSIG,
        $prefix . ':Transform'
    );

    $transfNode1->setAttribute(
        'Algorithm',
        $nsTransformMethod1
    );

    $transformsNode->appendChild(
        $transfNode1
    );

    /*
     * Transform C14N
     */
    $transfNode2 = $xmldoc->createElementNS(
        $nsDSIG,
        $prefix . ':Transform'
    );

    $transfNode2->setAttribute(
        'Algorithm',
        $nsTransformMethod2
    );

    $transformsNode->appendChild(
        $transfNode2
    );

    /*
     * ==========================================================
     * DIGEST METHOD
     * ==========================================================
     */
    $digestMethodNode = $xmldoc->createElementNS(
        $nsDSIG,
        $prefix . ':DigestMethod'
    );

    $digestMethodNode->setAttribute(
        'Algorithm',
        $nsDigestMethod
    );

    $referenceNode->appendChild(
        $digestMethodNode
    );

    /*
     * ==========================================================
     * DIGEST VALUE
     * ==========================================================
     */
    $digestValueNode = $xmldoc->createElementNS(
        $nsDSIG,
        $prefix . ':DigestValue',
        $digValue
    );

    $referenceNode->appendChild(
        $digestValueNode
    );

    /*
     * ==========================================================
     * CANONICALIZA SIGNEDINFO
     * ==========================================================
     */
    $cnSignedInfoNode = $signedInfoNode->C14N(
        $canonical[0],
        $canonical[1],
        $canonical[2],
        $canonical[3]
    );

    /*
     * ==========================================================
     * ASSINATURA RSA-SHA1
     * ==========================================================
     */
    $signature = '';

    if (!openssl_sign(
        $cnSignedInfoNode,
        $signature,
        $objSSLPriKey,
        OPENSSL_ALGO_SHA1
    )) {
        $msg = "Houve erro durante a assinatura digital.\n";

        $this->zGetOpenSSLError($msg);

        throw new \RuntimeException($msg);
    }

    $signatureValue = base64_encode(
        $signature
    );

    /*
     * ==========================================================
     * SIGNATURE VALUE
     * ==========================================================
     */
    $signatureValueNode = $xmldoc->createElementNS(
        $nsDSIG,
        $prefix . ':SignatureValue',
        $signatureValue
    );

    $signatureNode->appendChild(
        $signatureValueNode
    );

    /*
     * ==========================================================
     * KEY INFO
     * ==========================================================
     */
    $keyInfoNode = $xmldoc->createElementNS(
        $nsDSIG,
        $prefix . ':KeyInfo'
    );

    $signatureNode->appendChild(
        $keyInfoNode
    );

    /*
     * ==========================================================
     * X509 DATA
     * ==========================================================
     */
    $x509DataNode = $xmldoc->createElementNS(
        $nsDSIG,
        $prefix . ':X509Data'
    );

    $keyInfoNode->appendChild(
        $x509DataNode
    );

    /*
     * ==========================================================
     * CERTIFICADO
     * ==========================================================
     */
    $pubKeyClean = $this->zCleanPubKey();

    $x509CertificateNode = $xmldoc->createElementNS(
        $nsDSIG,
        $prefix . ':X509Certificate',
        $pubKeyClean
    );

    $x509DataNode->appendChild(
        $x509CertificateNode
    );

    /*
     * ==========================================================
     * RETORNA XML SEM XML DECLARATION
     * ==========================================================
     */
    return $xmldoc->saveXML(
        $xmldoc->documentElement,
        LIBXML_NOXMLDECL
    );
}

     /**
     * signatureExists
     * Check se o xml possi a tag Signature
     *
     * @param  DOMDocument $dom
     * @return boolean
     */
    private function zSignatureExists($dom)
    {
        $signature = $dom->getElementsByTagName('Signature')->item(0);
        if (! isset($signature)) {
            return false;
        }
        return true;
    }

    /**
     * zSignXML
     * Método que provê a assinatura do xml conforme padrão SEFAZ
     *
     * @param    DOMDocument $xmldoc
     * @param    DOMElement  $root
     * @param    DOMElement  $node
     * @param    resource    $objSSLPriKey
     * @return   string xml assinado
     * @internal param DOMDocument $xmlDoc
     */
    private function zSignXML($xmldoc, $root, $node, $objSSLPriKey)
    {
        $nsDSIG = 'http://www.w3.org/2000/09/xmldsig#';
        $nsCannonMethod = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
        $nsSignatureMethod = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';
        $nsTransformMethod1 ='http://www.w3.org/2000/09/xmldsig#enveloped-signature';
        $nsTransformMethod2 = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
        $nsDigestMethod = 'http://www.w3.org/2000/09/xmldsig#sha1';
        //pega o atributo id do node a ser assinado
        $idSigned = trim($node->getAttribute("Id"));
        //extrai os dados da tag para uma string na forma canonica
        $dados = $node->C14N(true, false, null, null);
        //calcular o hash dos dados
        $hashValue = hash('sha1', $dados, true);
        //converter o hash para base64
        $digValue = base64_encode($hashValue);
        //cria o node <Signature>
        //$signatureNode = $xmldoc->createElementNS($nsDSIG, 'Signature');
        $signatureNode = $xmldoc->createElement('Signature');
        //adiciona a tag <Signature> ao node raiz
        $root->appendChild($signatureNode);
        //cria o node <SignedInfo>
        $signedInfoNode = $xmldoc->createElement('SignedInfo');
        //adiciona o node <SignedInfo> ao <Signature>
        $signatureNode->appendChild($signedInfoNode);
        //cria no node com o método de canonização dos dados
        $canonicalNode = $xmldoc->createElement('CanonicalizationMethod');
        //adiona o <CanonicalizationMethod> ao node <SignedInfo>
        $signedInfoNode->appendChild($canonicalNode);
        //seta o atributo ao node <CanonicalizationMethod>
        $canonicalNode->setAttribute('Algorithm', $nsCannonMethod);
        //cria o node <SignatureMethod>
        $signatureMethodNode = $xmldoc->createElement('SignatureMethod');
        //adiciona o node <SignatureMethod> ao node <SignedInfo>
        $signedInfoNode->appendChild($signatureMethodNode);
        //seta o atributo Algorithm ao node <SignatureMethod>
        $signatureMethodNode->setAttribute('Algorithm', $nsSignatureMethod);
        //cria o node <Reference>
        $referenceNode = $xmldoc->createElement('Reference');
        //adiciona o node <Reference> ao node <SignedInfo>
        $signedInfoNode->appendChild($referenceNode);
        //seta o atributo URI a node <Reference>
        $referenceNode->setAttribute('URI', '#'.$idSigned);
        //cria o node <Transforms>
        $transformsNode = $xmldoc->createElement('Transforms');
        //adiciona o node <Transforms> ao node <Reference>
        $referenceNode->appendChild($transformsNode);
        //cria o primeiro node <Transform> OBS: no singular
        $transfNode1 = $xmldoc->createElement('Transform');
        //adiciona o primeiro node <Transform> ao node <Transforms>
        $transformsNode->appendChild($transfNode1);
        //set o atributo Algorithm ao primeiro node <Transform>
        $transfNode1->setAttribute('Algorithm', $nsTransformMethod1);
        //cria outro node <Transform> OBS: no singular
        $transfNode2 = $xmldoc->createElement('Transform');
        //adiciona o segundo node <Transform> ao node <Transforms>
        $transformsNode->appendChild($transfNode2);
        //set o atributo Algorithm ao segundo node <Transform>
        $transfNode2->setAttribute('Algorithm', $nsTransformMethod2);
        //cria o node <DigestMethod>
        $digestMethodNode = $xmldoc->createElement('DigestMethod');
        //adiciona o node <DigestMethod> ao node <Reference>
        $referenceNode->appendChild($digestMethodNode);
        //seta o atributo Algorithm ao node <DigestMethod>
        $digestMethodNode->setAttribute('Algorithm', $nsDigestMethod);
        //cria o node <DigestValue>
        $digestValueNode = $xmldoc->createElement('DigestValue', $digValue);
        //adiciona o node <DigestValue> ao node <Reference>
        $referenceNode->appendChild($digestValueNode);
        //extrai node <SignedInfo> para uma string na sua forma canonica
        $cnSignedInfoNode = $signedInfoNode->C14N(true, false, null, null);
        //cria uma variavel vazia que receberá a assinatura
        $signature = '';
        //calcula a assinatura do node canonizado <SignedInfo>
        //usando a chave privada em formato PEM
        if (! openssl_sign($cnSignedInfoNode, $signature, $objSSLPriKey)) {
            $msg = "Houve erro durante a assinatura digital.\n";
            $this->zGetOpenSSLError($msg);
            //while ($erro = openssl_error_string()) {
            //    $msg .= $erro . "\n";
            //}
            //throw new Exception\RuntimeException($msg);
        }
        //converte a assinatura em base64
        $signatureValue = base64_encode($signature);
        //cria o node <SignatureValue>
        $signatureValueNode = $xmldoc->createElement('SignatureValue', $signatureValue);
        //adiciona o node <SignatureValue> ao node <Signature>
        $signatureNode->appendChild($signatureValueNode);
        //cria o node <KeyInfo>
        $keyInfoNode = $xmldoc->createElement('KeyInfo');
        //adiciona o node <KeyInfo> ao node <Signature>
        $signatureNode->appendChild($keyInfoNode);
        //cria o node <X509Data>
        $x509DataNode = $xmldoc->createElement('X509Data');
        //adiciona o node <X509Data> ao node <KeyInfo>
        $keyInfoNode->appendChild($x509DataNode);
        //remove linhas desnecessárias do certificado
        $pubKeyClean = $this->zCleanPubKey();
        //cria o node <X509Certificate>
        $x509CertificateNode = $xmldoc->createElement('X509Certificate', $pubKeyClean);
        //adiciona o node <X509Certificate> ao node <X509Data>
        $x509DataNode->appendChild($x509CertificateNode);
        //salva o xml completo em uma string
        $xmlResp = $xmldoc->saveXML();
        //retorna o documento assinado
        return $xmlResp;
    }

    /* joga assinatura do termino da tag passada */
    private function zSignXMLMod2($xmldoc, $root, $node, $objSSLPriKey)
    {
        $nsDSIG = 'http://www.w3.org/2000/09/xmldsig#';
        $nsCannonMethod = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
        $nsSignatureMethod = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';
        $nsTransformMethod1 ='http://www.w3.org/2000/09/xmldsig#enveloped-signature';
        $nsTransformMethod2 = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
        $nsDigestMethod = 'http://www.w3.org/2000/09/xmldsig#sha1';

        // pega o atributo Id da tag a ser assinada
        $idSigned = trim($node->getAttribute("Id"));

        // Canonicaliza o conteúdo da tag
        $dados = $node->C14N(true, false, null, null);

        // Calcula hash SHA1
        $hashValue = hash('sha1', $dados, true);
        $digValue  = base64_encode($hashValue);

        // Cria a estrutura <Signature>
        $signatureNode = $xmldoc->createElementNS('$nsDSIG', 'Signature');
        // -------- SignedInfo --------
        $signedInfoNode = $xmldoc->createElement('SignedInfo');
        $signatureNode->appendChild($signedInfoNode);

        $canonicalNode = $xmldoc->createElement('CanonicalizationMethod');
        $canonicalNode->setAttribute('Algorithm', $nsCannonMethod);
        $signedInfoNode->appendChild($canonicalNode);

        $signatureMethodNode = $xmldoc->createElement('SignatureMethod');
        $signatureMethodNode->setAttribute('Algorithm', $nsSignatureMethod);
        $signedInfoNode->appendChild($signatureMethodNode);

        $referenceNode = $xmldoc->createElement('Reference');
        $referenceNode->setAttribute('URI', '#'.$idSigned);
        $signedInfoNode->appendChild($referenceNode);

        $transformsNode = $xmldoc->createElement('Transforms');
        $referenceNode->appendChild($transformsNode);

        $transfNode1 = $xmldoc->createElement('Transform');
        $transfNode1->setAttribute('Algorithm', $nsTransformMethod1);
        $transformsNode->appendChild($transfNode1);

        $transfNode2 = $xmldoc->createElement('Transform');
        $transfNode2->setAttribute('Algorithm', $nsTransformMethod2);
        $transformsNode->appendChild($transfNode2);

        $digestMethodNode = $xmldoc->createElement('DigestMethod');
        $digestMethodNode->setAttribute('Algorithm', $nsDigestMethod);
        $referenceNode->appendChild($digestMethodNode);

        $digestValueNode = $xmldoc->createElement('DigestValue', $digValue);
        $referenceNode->appendChild($digestValueNode);

        // Canonicaliza <SignedInfo> para assinar
        $cnSignedInfoNode = $signedInfoNode->C14N(true, false, null, null);

        // Assina com chave privada
        $signature = '';
        if (!openssl_sign($cnSignedInfoNode, $signature, $objSSLPriKey)) {
            $msg = "Erro ao assinar o XML.\n";
            $this->zGetOpenSSLError($msg);
        }
        $signatureValue = base64_encode($signature);

        // -------- SignatureValue --------
        $signatureValueNode = $xmldoc->createElement('SignatureValue', $signatureValue);
        $signatureNode->appendChild($signatureValueNode);

        // -------- KeyInfo --------
        $keyInfoNode = $xmldoc->createElement('KeyInfo');
        $signatureNode->appendChild($keyInfoNode);

        $x509DataNode = $xmldoc->createElement('X509Data');
        $keyInfoNode->appendChild($x509DataNode);

        // Certificado sem cabeçalhos
        $pubKeyClean = $this->zCleanPubKey();
        $x509CertificateNode = $xmldoc->createElement('X509Certificate', $pubKeyClean);
        $x509DataNode->appendChild($x509CertificateNode);

        // -------- INSERE a assinatura logo após o node assinado --------
        if ($node->nextSibling) {
            $node->parentNode->insertBefore($signatureNode, $node->nextSibling);
        } else {
            $node->parentNode->appendChild($signatureNode);
        }

        return $xmldoc->saveXML();
    }

    public function assinarPadraoNacional($docxml, $tagid = ''){
        $objSSLPriKey = openssl_get_privatekey($this->priKey);

        $xml = is_file($docxml) ? file_get_contents($docxml) : $docxml;

        $xml = str_replace(["\r\n", "\n", "\r", "\t"], '', $xml);

        $xmldoc = new \DOMDocument();
        $xmldoc->loadXML($xml);

        $node = $xmldoc->getElementsByTagName($tagid)->item(0);

        if (!$node) {
            throw new \Exception("A tag <$tagid> não existe no XML!");
        }

        // 🔥 GARANTE ID
        /*if (!$node->hasAttribute('Id')) {
            throw new \Exception("A tag <$tagid> precisa ter atributo Id.");
        }

        $this->docId = $node->getAttribute('Id');*/

        // 🔥 REGISTRA COMO ID REAL NO DOM
        //$node->setIdAttribute('Id', true);

        $root = $xmldoc->documentElement;

        return $this->zSignXMLTeste($xmldoc, $root, $node, $objSSLPriKey);
        //return $this->zSignXMLTeste($xmldoc, $root, $node, $objSSLPriKey);
    }



    /**
     * zLeaveParam
     * Limpa os parametros da classe
     */
    private function zLeaveParam()
    {
        $this->pfxCert='';
        $this->pubKey='';
        $this->priKey='';
        $this->certKey='';
        $this->pubKeyFile='';
        $this->priKeyFile='';
        $this->certKeyFile='';
        $this->expireTimestamp='';
    }
    
    /**
     * zGetOpenSSLError
     *
     * @param  string $msg
     * @return string
     */
    protected function zGetOpenSSLError($msg = '')
    {
        while ($erro = openssl_error_string()) {
            $msg .= $erro . "\n";
        }
        throw new Exception($msg);
    }

     /**
     * zCleanPubKey
     * Remove a informação de inicio e fim do certificado
     * contido no formato PEM, deixando o certificado (chave publica) pronta para ser
     * anexada ao xml da NFe
     *
     * @return string contendo o certificado limpo
     */
    protected function zCleanPubKey()
    {
        //inicializa variavel
        $data = '';
        //carregar a chave publica
        $pubKey = $this->pubKey;
        //carrega o certificado em um array usando o LF como referencia
        $arCert = explode("\n", $pubKey);
        foreach ($arCert as $curData) {
            //remove a tag de inicio e fim do certificado
            if (strncmp($curData, '-----BEGIN CERTIFICATE', 22) != 0
                && strncmp($curData, '-----END CERTIFICATE', 20) != 0
            ) {
                //carrega o resultado numa string
                $data .= trim($curData);
            }
        }
        return $data;
    }
}