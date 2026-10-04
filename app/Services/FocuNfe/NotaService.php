<?php

namespace App\Services\FocuNfe;

use App\Models\Empresa;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use RuntimeException;

class NotaService
{
    protected Client $client;

    public function __construct(Empresa $empresa)
    {
        $base_url = rtrim(env('FOCUNFE_API_URL'), '/') . '/';
        $token = $empresa->focunfe_token_prd;        
        
        if($empresa->ambiente_emissao == 'HOMOLOGACAO'){
            $base_url = rtrim(env('FOCUNFE_HMG_API_URL'), '/') . '/';
            $token = $empresa->focunfe_token_hmg;
        }

        $this->client = new Client([
            'base_uri' => $base_url,
            'timeout' => 30,
            'connect_timeout' => 10,
            'headers' => [
                'Content-Type' => 'application/json-patch+json',
                'Accept' => 'application/json',
                'authorization' => 'Basic ' . base64_encode($token)
            ],
        ]);
    }

    public function emitirNfse(array $dados){
        try {
            $response = $this->client->post("nfse", [
                'json' => $dados,
            ]);

            return json_decode(
                $response->getBody()->getContents(),
                true
            ) ?? [];
        } catch (ClientException $e) {
            $body = $e->getResponse()
                ? $e->getResponse()->getBody()->getContents()
                : null;

            throw new RuntimeException(
                'Erro ao Emitir NFSe no Portal Nota Fácil: ' .
                ($body ?: $e->getMessage()),
                $e->getCode(),
                $e
            );
        } catch (RequestException $e) {
            throw new RuntimeException(
                'Erro de comunicação com a API da Portal Nota Fácil: ' .
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function consultarNfse(string $referencia){
        try {
            $response = $this->client->get("nfse/{$referencia}");

            return json_decode(
                $response->getBody()->getContents(),
                true
            ) ?? [];
        } catch (ClientException $e) {
            $body = $e->getResponse()
                ? $e->getResponse()->getBody()->getContents()
                : null;

            throw new RuntimeException(
                'Erro ao Consultar NFSe no Portal Nota Fácil: ' .
                ($body ?: $e->getMessage()),
                $e->getCode(),
                $e
            );
        } catch (RequestException $e) {
            throw new RuntimeException(
                'Erro de comunicação com a API da Portal Nota Fácil: ' .
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}