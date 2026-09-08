<?php

namespace App\Services\Spedy;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use RuntimeException;

class SpedyService
{
    protected Client $client;
    /*
    $clienteCli é Utilizando para Empresas enviar as notas para respectivas empresas
    */
    protected Client $clienteCli;

    public function __construct($spedyApiKey = null)
    {
        $this->client = new Client([
            'base_uri' => rtrim(config('services.spedy.url'), '/') . '/',
            'timeout' => 30,
            'connect_timeout' => 10,
            'headers' => [
                'Content-Type' => 'application/json-patch+json',
                'Accept' => 'application/json',
                'X-Api-Key' => config('services.spedy.api_key'),
            ],
        ]);

        $this->clienteCli = new Client([
            'base_uri' => rtrim(config('services.spedy.url'), '/') . '/',
            'timeout' => 30,
            'connect_timeout' => 10,
            'headers' => [
                'Content-Type' => 'application/json-patch+json',
                'Accept' => 'application/json',
                'X-Api-Key' => $spedyApiKey,
            ],
        ]);
    }

    /**
     * Cadastra uma empresa na Spedy.
     */
    public function createCompany(array $data): array
    {
        try {
            $response = $this->client->post('companies', [
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
                'Erro ao cadastrar empresa na Portal Nota Fácil: ' .
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

    /**
     * Altera uma empresa na Spedy.
     *
     * @param string $id ID da empresa na Spedy
     * @param array $data Dados da empresa
     */
    public function updateCompany(string $id, array $data): array
    {
        try {
            $response = $this->client->put("companies/{$id}", [
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

    public function getCompanyById($id){
        try {
            $response = $this->client->get("companies/{$id}");

            return json_decode(
                $response->getBody()->getContents(),
                true
            ) ?? [];
        } catch (ClientException $e) {
            $body = $e->getResponse()
                ? $e->getResponse()->getBody()->getContents()
                : null;

            throw new RuntimeException(
                'Erro ao obter empresa na Portal Nota Fácil: ' .
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

    public function getCompanyList(){
        try {
            $response = $this->client->get("companies");

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

    public function serviceInvoicesCities(){
        try {
            $response = $this->client->get("service-invoices/cities?filterText=Anápolis");

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

    public function verificarCertificado($empresaId){
        try {
            $response = $this->client->get("companies/{$empresaId}/certificates");

            return json_decode(
                $response->getBody()->getContents(),
                true
            ) ?? [];
        } catch (ClientException $e) {
            $body = $e->getResponse()
                ? $e->getResponse()->getBody()->getContents()
                : null;

            throw new RuntimeException(
                'Erro ao obter empresa na Portal Nota Fácil: ' .
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

    public function uploadCertificate(
        string $companyId,
        string $certificatePath,
        string $password
    ): array {
        try {
            $response = $this->client->post(
                "companies/{$companyId}/certificates",
                [
                    'multipart' => [
                        [
                            'name' => 'certificateFile',
                            'contents' => fopen($certificatePath, 'rb'),
                            'filename' => basename($certificatePath),
                            'headers' => [
                                'Content-Type' => 'application/octet-stream',
                            ],
                        ],
                        [
                            'name' => 'password',
                            'contents' => $password,
                        ],
                    ],
                ]
            );

            return json_decode(
                $response->getBody()->getContents(),
                true
            ) ?? [];

        } catch (ClientException $e) {
            $body = $e->getResponse()
                ? $e->getResponse()->getBody()->getContents()
                : null;

            throw new RuntimeException(
                'Erro ao enviar certificado para a Portal Nota Fácil: ' .
                ($body ?: $e->getMessage()),
                $e->getCode(),
                $e
            );
        } catch (RequestException $e) {
            throw new RuntimeException(
                'Erro de comunicação com a API da Spedy: ' .
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function getConfigurations($empresaId){
        try {
            //https://api.spedy.com.br/v1/companies/{id}/settings
            $response = $this->client->get("companies/{$empresaId}/settings");

            return json_decode(
                $response->getBody()->getContents(),
                true
            ) ?? [];
        } catch (ClientException $e) {
            $body = $e->getResponse()
                ? $e->getResponse()->getBody()->getContents()
                : null;

            throw new RuntimeException(
                'Erro ao obter empresa na Portal Nota Fácil: ' .
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

    public function setConfigurations($empresaId, array $data){
        try {
            $response = $this->client->put("companies/{$empresaId}/settings", [
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

    public function createNfse(array $data){
        try {
            $response = $this->clienteCli->post('service-invoices', [
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
                'Erro ao cadastrar empresa na Portal Nota Fácil: ' .
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

    public function consultarNfse($invoiceId){
        try {
            //https://api.spedy.com.br/v1/companies/{id}/settings
            $response = $this->client->get("service-invoices/{$invoiceId}");

            return json_decode(
                $response->getBody()->getContents(),
                true
            ) ?? [];
        } catch (ClientException $e) {
            $body = $e->getResponse()
                ? $e->getResponse()->getBody()->getContents()
                : null;

            throw new RuntimeException(
                'Erro ao obter empresa na Portal Nota Fácil: ' .
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