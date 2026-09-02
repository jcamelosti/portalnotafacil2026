<?php

namespace JCamelo\NfseNacionalLib\Factories;

class XmlFactory
{
    public static function gerarXmlConsultaDadosCadastrais(string $cnpj, string $im): string
    {
        return <<<XML
        <ConsultarDadosCadastraisEnvio xmlns="http://www.sped.fazenda.gov.br/nfse">
            <Prestador>
                <CNPJ>{$cnpj}</CNPJ>
                <IM>{$im}</IM>
            </Prestador>
        </ConsultarDadosCadastraisEnvio>
        XML;
    }

    public static function gerarXmlConsultaUrlNfse(string $cnpj, string $im, int $numero_nfse, string $data_inicial, string $data_final): string
    {
        /*return <<<XML
        <ConsultarUrlNfseEnvio xmlns="http://www.sped.fazenda.gov.br/nfse">
            <NumeroNfse>{$numero_nfse}</NumeroNfse>
            <Prestador>
                <CNPJ>{$cnpj}</CNPJ>
                <IM>{$im}</IM>
            </Prestador>
            <PeriodoEmissao>
                <DataInicial>{$data_inicial}</DataInicial>
                <DataFinal>{$data_final}</DataFinal>
            </PeriodoEmissao>
            <Pagina>1</Pagina>
        </ConsultarUrlNfseEnvio>
        XML;*/
        return <<<XML
        <ConsultarUrlNfseEnvio xmlns="http://www.sped.fazenda.gov.br/nfse">
            <Prestador>
                <CNPJ>{$cnpj}</CNPJ>
                <IM>{$im}</IM>
            </Prestador>
            <NumeroNfse>{$numero_nfse}</NumeroNfse>
            <Pagina>1</Pagina>
        </ConsultarUrlNfseEnvio>
        XML;
    }
}