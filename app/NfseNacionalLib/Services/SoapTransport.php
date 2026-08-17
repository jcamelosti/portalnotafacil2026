<?php
namespace JCamelo\NfseNacionalLib\Services;

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
}