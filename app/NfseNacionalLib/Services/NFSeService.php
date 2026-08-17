<?php
namespace JCamelo\NfseNacionalLib\Services;

use Illuminate\Support\Facades\Log;
use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;
use JCamelo\NfseNacionalLib\Factories\DPSFactory;
use JCamelo\NfseNacionalLib\Factories\NFSeProviderFactory;
use JCamelo\NfseNacionalLib\Factories\XmlFactory;
use JCamelo\NfseNacionalLib\XML\Signer\XmlSigner;

class NFSeService
{
    public function gerarNfse(string $provider, DPSDataDTO $data, int $empresaId)
    {
        // 🔥 1. GERAR XML (usa seu Factory + Builders)
        $xml = DPSFactory::make($data);

        // 🔥 2. ASSINAR XML
        $signedXml = app(XmlSigner::class)->assinarXml($xml, $empresaId, 'infDPS');
        
        $signedXml = str_replace('<?xml version="1.0"?>', '', $signedXml);
        Log::info("XML ASSINADO: " . $signedXml);
        //dd($signedXml);
        // 🔥 3. ESCOLHER PROVIDER
        $driver = NFSeProviderFactory::make($provider);

        // 🔥 4. ENVIAR
        return $driver->gerarNfse($signedXml, $empresaId);
    }
    
    public function consultarDadosCadastrais(string $provider, int $empresaId, string $cnpj, string $im)
    {
        $xml = XmlFactory::gerarXmlConsultaDadosCadastrais($cnpj, $im);
        
        $driver = NFSeProviderFactory::make($provider);

        return $driver->consultarDadosCadastrais($xml, $empresaId);
    }

    public function consultarUrlNfse(string $provider, int $empresaId, string $cnpj, string $im, int $numero_nfse, string $data_inicial, string $data_final)
    {
        $xml = XmlFactory::gerarXmlConsultaUrlNfse($cnpj, $im, $numero_nfse, $data_inicial, $data_final);
        
        $driver = NFSeProviderFactory::make($provider);

        return $driver->consultarUrlNfse($xml, $empresaId);
    }

    public function recepcionarLoteDpsSincrono(string $provider, DPSDataDTO $data, int $empresaId)
    {
        // 🔥 1. GERAR XML (usa seu Factory + Builders)
        $xml = DPSFactory::makeRecepcionarLoteDpsSincrono($data);

        // 🔥 2. ASSINAR XML
        $signedXml = app(XmlSigner::class)->assinarXml($xml, $empresaId, 'infDPS');
        $signedXml = app(XmlSigner::class)->assinarXml($signedXml, $empresaId, 'LoteDps');
        $signedXml = str_replace('<?xml version="1.0"?>', '', $signedXml);
        //Log::info("XML ASSINADO: " . $signedXml);
        //dd($signedXml);
        // 🔥 3. ESCOLHER PROVIDER
        $driver = NFSeProviderFactory::make($provider);

        // 🔥 4. ENVIAR
        return $driver->recepcionarLoteDpsSincrono($signedXml, $empresaId);
    }
}