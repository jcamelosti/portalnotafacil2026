<?php
namespace JCamelo\NfseNacionalLib\Providers;

use JCamelo\NfseNacionalLib\Contracts\NFSeProviderInterface;
use JCamelo\NfseNacionalLib\Services\ISSNetService;  

class ISSNetProvider implements NFSeProviderInterface
{
    public function gerarNfse(string $xml, int $empresaId)
    {
        return app(ISSNetService::class)
            ->gerarNfse($xml, $empresaId);
    }

    public function validarXml(string $xml, int $empresaId){
        return app(ISSNetService::class)
            ->validarXml($xml, $empresaId);
    }

    public function cancelarNfse(string $xml)
    {
        return app(ISSNetService::class)->cancelarNfse($xml);
    }

    public function consultarNfse(string $xml)
    {
        return app(ISSNetService::class)->consultarNfseDps($xml);
    }

    public function consultarLote(string $xml)
    {
        return app(ISSNetService::class)->consultarLoteDps($xml);
    }

    public function consultarXml(string $xml, int $empresaId){
         return app(ISSNetService::class)
            ->consultarXml($xml, $empresaId);
    }

    public function consultarDadosCadastrais(string $xml, int $empresaId)
    {
        return app(ISSNetService::class)
            ->consultarDadosCadastrais($xml, $empresaId);
    }

    public function consultarUrlNfse(string $xml, int $empresaId)
    {
        return app(ISSNetService::class)
            ->consultarUrlNfse($xml, $empresaId);
    }

    public function recepcionarLoteDpsSincrono(string $xml, int $empresaId)
    {
        return app(ISSNetService::class)
            ->recepcionarLoteDpsSincrono($xml, $empresaId);
    }

    public function consultarNfseServicosPrestados(string $xml, int $empresaId){
        return app(ISSNetService::class)
            ->consultarNfseServicosPrestados($xml, $empresaId);
    }

    public function consultarNfseServicosTomados(string $xml, int $empresaId){
        return app(ISSNetService::class)
            ->consultarNfseServicosTomados($xml, $empresaId);
    }
}