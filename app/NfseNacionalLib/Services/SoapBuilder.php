<?php
namespace JCamelo\NfseNacionalLib\Services;

use Illuminate\Support\Facades\Log;

class SoapBuilder
{
    public static function build(string $method, string $xml): string
    {
        // Cabeçalho padrão NFS-e Nacional
        $cabecalho = <<<XML
<cabecalho xmlns="http://www.sped.fazenda.gov.br/nfse" versao="1.01">
  <versaoDados>1.01</versaoDados>
</cabecalho>
XML;
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope 
    xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:ws="http://www.sped.fazenda.gov.br/nfse">
    <soapenv:Header/>
    <soapenv:Body>
        <ws:$method>
            <ws:nfseCabecMsg>             
                $cabecalho
            </ws:nfseCabecMsg>
            <ws:nfseDadosMsg>
                $xml
            </ws:nfseDadosMsg>
        </ws:$method>
    </soapenv:Body>
</soapenv:Envelope>
XML;
    }
}