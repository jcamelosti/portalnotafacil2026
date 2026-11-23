<?php

namespace App\Services\FocuNfe;

use App\Models\Empresa;
use App\Services\FocuNfe\Factories\FocunfseFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use InvalidArgumentException;
use JCamelo\NfseNacionalLib\DTO\DPSDataSnDTO;

class FocunfseService
{
    public function __construct(
        private FocunfseFactory $factory,
        private NotaService $emissor
    ) {}

    /**
     * Gera o payload de acordo com o município da empresa.
     */
    public function gerarPayload(
        Empresa $empresa,
        DPSDataSnDTO $dados
    ): array {

        $municipio = $this->factory->make($empresa);
        
        return $municipio->gerarPayload(
            $empresa,
            $dados
        );
    }

    /**
     * Envia os dados para a API Focunfse.
     */
    public function enviar(
        Empresa $empresa,
        array $dados
    ): Response {
        dd($this->emissor->emitirNfse($dados));
        
        /*$url = config('services.focunfse.url');
        $token = config('services.focunfse.token');

        if (!$url || !$token) {
            throw new InvalidArgumentException(
                'Configurações da API Focunfse não encontradas.'
            );
        }

        return Http::withToken($token)
            ->acceptJson()
            ->asJson()
            ->timeout(60)
            ->post($url, $payload)
            ->throw();*/
        
        /* return [
            'error' => false,
            'mensagem' => 'Nota Emitida com Sucesso',
            'registro' => $ne
        ];*/

        /*return [
            'error' => true,
            'mensagem' => 'Erros ao emitir NFSe: ' . $mensagemErro,
            'registro' => null
        ];*/
    }
}