<?php

namespace App\Services\FocuNfe;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use RuntimeException;

class GestaoService
{
    protected Client $client;

    public function __construct()
    {
        //FOCUNFE_API_KEY="fVLerxmMi9llhn9UXDAkhnDaQVveBBZX"
        //FOCUNFE_API_URL="https://homologacao.focusnfe.com.br/v2"
        $this->client = new Client([
            'base_uri' => rtrim(env('FOCUNFE_API_URL'), '/') . '/',
            'timeout' => 30,
            'connect_timeout' => 10,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'authorization' => 'Basic ' . base64_encode(env('FOCUNFE_API_KEY'))
            ],
        ]);
    }

    public function obterEmpresas(){
        try {
            $response = $this->client->get("empresas");

            return json_decode(
                $response->getBody()->getContents(),
                true
            ) ?? [];
        } catch (ClientException $e) {
            $body = $e->getResponse()
                ? $e->getResponse()->getBody()->getContents()
                : null;

            throw new RuntimeException(
                'Erro ao obter empresas na Portal Nota Fácil: ' .
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

    public function obterEmpresaPorId($id){
        try {
            $response = $this->client->get("empresas/{$id}");

            return json_decode(
                $response->getBody()->getContents(),
                true
            ) ?? [];
        } catch (ClientException $e) {
            $body = $e->getResponse()
                ? $e->getResponse()->getBody()->getContents()
                : null;

            throw new RuntimeException(
                'Erro ao obter empresas na Portal Nota Fácil: ' .
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

    public function atualizarEmpresa(string $id, array $data): array
    {
        try {
            $response = $this->client->put("empresas/{$id}", [
                'json' => $data,
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
                'Erro ao alterar empresa na Portal Nota Fácil: ' .
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

    public function criarEmpresa(array $data): array
    {
        try {
            $response = $this->client->post("empresas", [
                'json' => $data,
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
                'Erro ao alterar empresa na Portal Nota Fácil: ' .
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