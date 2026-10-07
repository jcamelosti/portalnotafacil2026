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

        //formatando xml para Log
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xml);
        Log::info($domxml->saveXML());
             
        // 🔥 4. ENVIAR
        return $driver->gerarNfse($xml, $empresaId);
    }

    public function validarXml(string $provider, DPSDataSnDTO $data, int $empresaId){
        $builder = new DPSSnXmlBuilder();
        $xml = $builder->build($data);
        Log::info('Log Xml Única Linha - Validação');
        Log::info($xml);
        
        $assinador = app(XmlSigner::class);
        
        // 🔥 1. GERAR XML (usa seu Factory + Builders)
        $xml = DPSFactory::make($data, $xml);

        // 🔥 3. ESCOLHER PROVIDER
        $driver = NFSeProviderFactory::make($provider);

        $xml = $assinador->sign($empresaId, $xml, 'infDPS', '', 'DPS');
        
        // 🔥 4. ENVIAR
        return $driver->validarXml($xml, $empresaId);
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

        Log::info($xml);

        return $driver->consultarUrlNfse($xml, $empresaId);
    }

    public function consultarXml(string $provider, int $empresaId, string $cnpj, string $im, int $numero_nfse, string $data_inicial, string $data_final){
        $xml = XmlFactory::consultarXml($cnpj, $im, $numero_nfse, $data_inicial, $data_final);

        $driver = NFSeProviderFactory::make($provider);
        
        Log::info($xml);

        return $driver->consultarXml($xml, $empresaId);
    }

    public function consultarNfseServicosPrestados(
        string $provider, 
        int $empresaId, 
        string $cnpj, 
        string $im, 
        ?int $numero_nfse, 
        string $data_inicial, 
        string $data_final, 
        ?int $pagina
    ){
        $pagina = is_null($pagina) ? 1 : $pagina;
        $xml = XmlFactory::consultarNfseServicosPrestados($cnpj, $im, $numero_nfse, $data_inicial, $data_final, $pagina);
        
        $driver = NFSeProviderFactory::make($provider);
        
        Log::info($xml);
        return $driver->consultarNfseServicosPrestados($xml, $empresaId);
    }

    public function consultarNfseServicosTomados
    (
        string $provider, 
        int $empresaId, 
        string $cnpj, 
        string $im, 
        ?int $numero_nfse, 
        string $data_inicial, 
        string $data_final, 
        ?int $pagina
    ){
        $pagina = is_null($pagina) ? 1 : $pagina;

        $xml = XmlFactory::consultarNfseServicosTomados(
            $cnpj, // CNPJ consulente
            null,              // CPF consulente
            $im,          // IM consulente

            $numero_nfse,             // Número NFS-e
            $data_inicial,              // Data inicial emissão
            $data_final,              // Data final emissão
            null,              // Data inicial competência
            null,              // Data final competência

            $cnpj,  // CNPJ tomador
            null,              // CPF tomador
            $im,          // IM tomador

            null,              // CNPJ intermediário
            null,              // CPF intermediário
            null,              // IM intermediário

            $pagina                 // Página
        );
        
        $driver = NFSeProviderFactory::make($provider);

        return $driver->consultarNfseServicosTomados($xml, $empresaId);
    }
}