<?php
namespace JCamelo\NfseNacionalLib\Services;

use Illuminate\Support\Facades\Log;
use JCamelo\NfseNacionalLib\DTO\DPSDataDTO;
use JCamelo\NfseNacionalLib\DTO\DPSDataSnDTO;
use JCamelo\NfseNacionalLib\Factories\DPSFactory;
use JCamelo\NfseNacionalLib\Factories\NFSeProviderFactory;
use JCamelo\NfseNacionalLib\Factories\XmlFactory;
use JCamelo\NfseNacionalLib\XML\Builders\DPSSnXmlBuilder;
use JCamelo\NfseNacionalLib\XML\Signer\XmlSigner;

class NFSeService
{
    public function gerarNfse(string $provider, DPSDataSnDTO $data, int $empresaId)
    {
        $builder = new DPSSnXmlBuilder();
        $xml = $builder->build($data);
        Log::info('Log Xml Única Linha');
        Log::info($xml);
        
        $assinador = app(XmlSigner::class);
        
        //$xml = $assinador->sign($empresaId, $xml, 'infDPS', '', 'DPS');
        //Log::info('Xml Assinado');
        //Log::info($xml);

        // 🔥 1. GERAR XML (usa seu Factory + Builders)
        $xml = DPSFactory::make($data, $xml);
        
        Log::info('XML Completo para Envio');
        Log::info($xml);
                
        // 🔥 3. ESCOLHER PROVIDER
        $driver = NFSeProviderFactory::make($provider);


        $xml = $assinador->sign($empresaId, $xml, 'infDPS', '', 'DPS');
        Log::info('Xml Assinado');
        Log::info($xml);
        
        // 🔥 4. ENVIAR
        return $driver->gerarNfse($xml, $empresaId);
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
        $assinador = app(XmlSigner::class);
		$xml = $assinador->assinar($xml, 'infDPS', $empresaId);
		$xml = str_replace('<?xml version="1.0"?>', '', $xml);

        // 🔥 3. ESCOLHER PROVIDER
        $driver = NFSeProviderFactory::make($provider);

        // 🔥 4. ENVIAR
        return $driver->recepcionarLoteDpsSincrono($xml, $empresaId);
    }
}