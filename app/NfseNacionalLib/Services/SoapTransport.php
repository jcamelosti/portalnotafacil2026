<?php
namespace JCamelo\NfseNacionalLib\Services;

use Illuminate\Support\Facades\Log;
use NFePHP\Common\DOMImproved as Dom;

class SoapTransport
{
    public function send(string $url, string $uri, string $method, string $xml, array $cert)
    {
        $client = new \SoapClient(null, [
            'location' => $url,
            'uri' => $uri,
            'trace' => 1,
            'exceptions' => true,
            'local_cert' => $cert['cert'],
            'passphrase' => $cert['password'],
        ]);

        $response = $client->__doRequest(
            $xml,
            $url,
            $uri . '/' . $method,
            SOAP_1_1
        );

        return $response;
    }


    public function enviarRequisicao(string $url, string $uri, string $method, string $xml, array $cert){
        $wsdl       = $url;
        $endPoint   = $url;

        $options = [
            'location' => $endPoint,
            'keep_alive' => false,//true
            'trace' => true,
            'local_cert' => $cert['cert'],
            'passphrase' => $cert['password'],
            'cache_wsdl' => 0,
            'wsdl_cache' => WSDL_CACHE_NONE,
            'exceptions' => true,
            'use' => SOAP_ENCODED,
            //'soap_version' => SOAP_1_2,
            'stream_context'=> stream_context_create([
                'http' => [
                    'protocol_version'=>'1.1',
                    'header' => 'Connection: Close',
                    'user_agent' => 'PHPSoapClient'
                ],
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ]),
        ];

        try {
            $client = new \SoapClient($wsdl, $options);

            /*$dom = new Dom('1.0', 'utf-8');
            $root = $dom->createElement('cabecalho');    

            //versao - atributo do cabecalho
            $domAttribute = $dom->createAttribute('versao');
            $domAttribute->value = '1.00';
            $root->appendChild($domAttribute);

            //xmlns
            $domAttribute = $dom->createAttribute('xmlns');
            $domAttribute->value = 'http://www.abrasf.org.br/nfse.xsd';
            $root->appendChild($domAttribute);

            $dom->addChild(
                $root,
                'versaoDados',
                '2.04',
                true,
                "Versao",
                true
            );
        
            $dom->appendChild($root);*/

            /*$dom = new Dom('1.0', 'utf-8');
            $root = $dom->createElement('cabecalho');

            // atributo versao
            $domAttribute = $dom->createAttribute('versao');
            $domAttribute->value = '1.01';
            $root->appendChild($domAttribute);

            // atributo xmlns
            $domAttribute = $dom->createAttribute('xmlns');
            $domAttribute->value = 'http://www.sped.fazenda.gov.br/nfse';
            $root->appendChild($domAttribute);

            // versaoDados
            $dom->addChild(
                $root,
                'versaoDados',
                '1.01',
                true,
                'Versao',
                true
            );

            // adiciona a raiz ao documento
            $dom->appendChild($root);

            $xmlCabec = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dom->saveXML());   */
            //$xmlCabec='<cabecalho xmlns="http://www.sped.fazenda.gov.br/nfse" versao="1.01"><versaoDados>1.01</versaoDados></cabecalho>';
             $cabecalho = <<<XML
<cabecalho xmlns="http://www.sped.fazenda.gov.br/nfse" versao="1.01">
<versaoDados>1.01</versaoDados>
</cabecalho>
XML;
            $arguments = [$method => [
                'nfseCabecMsg' => $cabecalho,
                'nfseDadosMsg' => $xml,
                ]
            ];

            $options = [];
            $client->__soapCall($method, $arguments, $options);
            //Log::info($client->__getLastRequest());

            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $client->__getLastResponse());
            //Log::info("Resultado Envio WebService");
            //Log::info($response);
            $xml = simplexml_load_string( $response );
            dd($xml);
            return (!is_null($xml->sBody) || !empty($xml->sBody)) || isset($xml->sBody) ? $xml->sBody : null;
        } catch (\Exception $e) {
            echo $e->getFile();
            echo " - Erro Interno Envia Requisição - Linha: " . $e->getLine();
            echo "<pre>";
            print_r( $e->getMessage() );
        }
    }
}