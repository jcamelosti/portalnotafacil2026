<?php
namespace JCamelo\NfseNacionalLib\Contracts;

interface NFSeProviderInterface
{
    public function gerarNfse(string $xml, int $empresaId);
    public function cancelarNfse(string $xml);
    public function consultarNfse(string $xml);
    public function consultarLote(string $xml);
    public function consultarDadosCadastrais(string $xml, int $empresaId);
    public function recepcionarLoteDpsSincrono(string $xml, int $empresaId);
}