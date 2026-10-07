<?php

namespace JCamelo\NfseNacionalLib\Factories;

use JCamelo\NfseNacionalLib\Utils\XmlUtils;

class XmlFactory
{
    use XmlUtils;

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
            <Pagina>1</Pagina>
        </ConsultarUrlNfseEnvio>
        XML;*/

        return <<<XML
        <ConsultarUrlNfseEnvio xmlns="http://www.sped.fazenda.gov.br/nfse"><Prestador><CNPJ>{$cnpj}</CNPJ><IM>{$im}</IM></Prestador><NumeroNfse>{$numero_nfse}</NumeroNfse><Pagina>1</Pagina></ConsultarUrlNfseEnvio>
        XML;
    }

    public static function consultarXml(string $cnpj, string $im, int $numero_nfse, string $data_inicial, string $data_final): string{
        return <<<XML
        <ConsultarNfseServicoPrestadoEnvio xmlns="http://www.sped.fazenda.gov.br/nfse"><Prestador><CNPJ>{$cnpj}</CNPJ><IM>{$im}</IM></Prestador><NumeroNfse>{$numero_nfse}</NumeroNfse><Pagina>1</Pagina></ConsultarNfseServicoPrestadoEnvio>
        XML;
    }

    public static function consultarNfseServicosPrestados(
        string $cnpj,
        ?string $im,
        ?string $numeroNfse,
        ?string $dataInicial,
        ?string $dataFinal,
        int $pagina
    ): string
    {

        $dom = new \DOMDocument('1.0', 'UTF-8');

        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        $root = $dom->createElementNS(
            'http://www.sped.fazenda.gov.br/nfse',
            'ConsultarNfseServicoPrestadoEnvio'
        );

        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:ns2',
            'http://www.w3.org/2000/09/xmldsig#'
        );

        $dom->appendChild($root);

        // Prestador
        $prestador = $dom->createElement('Prestador');
        $root->appendChild($prestador);

        self::appendText($dom, $prestador, 'CNPJ', $cnpj);

        if (!empty($im)) {
            self::appendText($dom, $prestador, 'IM', $im);
        }

        // CHOICE
        if (!empty($numeroNfse)) {

            self::appendText(
                $dom,
                $root,
                'NumeroNfse',
                $numeroNfse
            );

        } elseif (!empty($dataInicial) && !empty($dataFinal)) {

            $periodoEmissao = $dom->createElement('PeriodoEmissao');
            $root->appendChild($periodoEmissao);

            self::appendText(
                $dom,
                $periodoEmissao,
                'DataInicial',
                $dataInicial
            );

            self::appendText(
                $dom,
                $periodoEmissao,
                'DataFinal',
                $dataFinal
            );
        }

        // Paginação
        self::appendText(
            $dom,
            $root,
            'Pagina',
            $pagina
        );

        return $dom->saveXML($root);
    }

    public static function consultarNfseServicosTomados(
        string $cnpjConsulente,
        ?string $cpfConsulente,
        ?string $imConsulente,
        ?string $numeroNfse,
        ?string $dataInicial,
        ?string $dataFinal,
        ?string $dataCompetenciaInicial,
        ?string $dataCompetenciaFinal,
        ?string $cnpjTomador,
        ?string $cpfTomador,
        ?string $imTomador,
        ?string $cnpjIntermediario,
        ?string $cpfIntermediario,
        ?string $imIntermediario,
        int $pagina
    ): string {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        // =========================================================
        // ROOT
        // =========================================================

        $root = $dom->createElementNS(
            'http://www.sped.fazenda.gov.br/nfse',
            'ConsultarNfseServicoTomadoEnvio'
        );

        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:ns2',
            'http://www.w3.org/2000/09/xmldsig#'
        );

        $dom->appendChild($root);

        // =========================================================
        // CONSULENTE
        // =========================================================

        $consulente = $dom->createElement('Consulente');

        $root->appendChild($consulente);

        // CNPJ ou CPF
        if (!empty($cnpjConsulente)) {

            self::appendText(
                $dom,
                $consulente,
                'CNPJ',
                $cnpjConsulente
            );

        } elseif (!empty($cpfConsulente)) {

            self::appendText(
                $dom,
                $consulente,
                'CPF',
                $cpfConsulente
            );

        } else {

            throw new \InvalidArgumentException(
                'Informe o CNPJ ou CPF do consulente.'
            );
        }

        // IM opcional
        self::appendOptionalText(
            $dom,
            $consulente,
            'IM',
            $imConsulente
        );

        // =========================================================
        // CHOICE
        // NumeroNfse OU PeriodoEmissao OU PeriodoCompetencia
        // =========================================================

        if (!empty($numeroNfse)) {

            self::appendText(
                $dom,
                $root,
                'NumeroNfse',
                $numeroNfse
            );

        } elseif (!empty($dataInicial) && !empty($dataFinal)) {

            $periodoEmissao = $dom->createElement(
                'PeriodoEmissao'
            );

            $root->appendChild($periodoEmissao);

            self::appendText(
                $dom,
                $periodoEmissao,
                'DataInicial',
                $dataInicial
            );

            self::appendText(
                $dom,
                $periodoEmissao,
                'DataFinal',
                $dataFinal
            );

        } elseif (
            !empty($dataCompetenciaInicial) &&
            !empty($dataCompetenciaFinal)
        ) {

            $periodoCompetencia = $dom->createElement(
                'PeriodoCompetencia'
            );

            $root->appendChild($periodoCompetencia);

            self::appendText(
                $dom,
                $periodoCompetencia,
                'DataInicial',
                $dataCompetenciaInicial
            );

            self::appendText(
                $dom,
                $periodoCompetencia,
                'DataFinal',
                $dataCompetenciaFinal
            );

        } else {

            throw new \InvalidArgumentException(
                'Informe NumeroNfse, PeriodoEmissao ou PeriodoCompetencia.'
            );
        }

        // =========================================================
        // CHOICE
        // Tomador OU Intermediario
        // =========================================================

        if (
            !empty($cnpjTomador) ||
            !empty($cpfTomador)
        ) {

            $tomador = $dom->createElement(
                'Tomador'
            );

            $root->appendChild($tomador);

            // CNPJ ou CPF
            if (!empty($cnpjTomador)) {

                self::appendText(
                    $dom,
                    $tomador,
                    'CNPJ',
                    $cnpjTomador
                );

            } else {

                self::appendText(
                    $dom,
                    $tomador,
                    'CPF',
                    $cpfTomador
                );
            }

            // IM opcional
            self::appendOptionalText(
                $dom,
                $tomador,
                'IM',
                $imTomador
            );

        } elseif (
            !empty($cnpjIntermediario) ||
            !empty($cpfIntermediario)
        ) {

            $intermediario = $dom->createElement(
                'Intermediario'
            );

            $root->appendChild($intermediario);

            // CNPJ ou CPF
            if (!empty($cnpjIntermediario)) {

                self::appendText(
                    $dom,
                    $intermediario,
                    'CNPJ',
                    $cnpjIntermediario
                );

            } else {

                self::appendText(
                    $dom,
                    $intermediario,
                    'CPF',
                    $cpfIntermediario
                );
            }

            // IM opcional
            self::appendOptionalText(
                $dom,
                $intermediario,
                'IM',
                $imIntermediario
            );

        } else {

            throw new \InvalidArgumentException(
                'Informe o Tomador ou o Intermediario.'
            );
        }

        // =========================================================
        // PAGINAÇÃO
        // =========================================================

        self::appendText(
            $dom,
            $root,
            'Pagina',
            $pagina
        );

        // XML em uma única linha
        return $dom->saveXML($root);
    }
}