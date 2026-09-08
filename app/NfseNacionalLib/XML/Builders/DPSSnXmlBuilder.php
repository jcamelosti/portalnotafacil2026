<?php

namespace JCamelo\NfseNacionalLib\XML\Builders;

use JCamelo\NfseNacionalLib\DTO\DPSDataSnDTO;
use DOMDocument;
use DOMElement;
use InvalidArgumentException;

class DPSSnXmlBuilder
{
    private const NS_NFSE = 'http://www.sped.fazenda.gov.br/nfse';

    public function build(DPSDataSnDTO $data): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        /*
         * <DPS versao="1.01">
         */
        /*$dps = $dom->createElement(
            'DPS'
        );*/

        $dps = $dom->createElement(
            'DPS'
        );

        $dps->setAttribute(
            'versao',
            $data->versao ?? '1.01'
        );

        $dom->appendChild($dps);

        /*
         * <infDPS Id="...">
         */
        /*$infDps = $dom->createElement(
            'infDPS'
        );*/

        $infDps = $dom->createElement('infDPS');

        $infDps->setAttribute(
            'Id',
            $this->generateId($data)
        );

        $dps->appendChild($infDps);

        /*
         * =========================================================
         * DADOS DA DPS
         * =========================================================
         */

        $this->appendText(
            $dom,
            $infDps,
            'tpAmb',
            $data->ambiente
        );

        $this->appendText(
            $dom,
            $infDps,
            'dhEmi',
            $data->dataEmissao
        );

        $this->appendText(
            $dom,
            $infDps,
            'verAplic',
            $data->verAplic ?? '1.01'
        );

        $this->appendText(
            $dom,
            $infDps,
            'serie',
            $data->serie
        );

        $this->appendText(
            $dom,
            $infDps,
            'nDPS',
            $data->numDps
        );

        $this->appendText(
            $dom,
            $infDps,
            'dCompet',
            $data->dataCompetencia
        );

        $this->appendText(
            $dom,
            $infDps,
            'tpEmit',
            1
        );

        $this->appendText(
            $dom,
            $infDps,
            'cLocEmi',
            $data->codigoMunicipio
        );

        /*
         * =========================================================
         * PRESTADOR
         * =========================================================
         */

        /*$prest = $dom->createElement(
            'prest'
        );*/
        $prest = $dom->createElement(
            'prest'
        );

        $infDps->appendChild($prest);

        $this->appendOptionalText(
            $dom,
            $prest,
            'CNPJ',
            $data->cnpjPrestador
        );

        $this->appendOptionalText(
            $dom,
            $prest,
            'IM',
            $data->imPrestador
        );

        $this->appendOptionalText(
            $dom,
            $prest,
            'fone',
            $data->fonePrestador
        );

        $this->appendOptionalText(
            $dom,
            $prest,
            'email',
            $data->emailPrestador
        );

        /*
         * regTrib
         */
        /*$regTrib = $dom->createElement(
            'regTrib'
        );*/

        $regTrib = $dom->createElement(
            'regTrib'
        );

        $prest->appendChild($regTrib);

        $this->appendOptionalText(
            $dom,
            $regTrib,
            'opSimpNac',
            $data->opSimpNac
        );

        $this->appendOptionalText(
            $dom,
            $regTrib,
            'regApTribSN',
            $data->regApTribSN
        );

        $this->appendOptionalText(
            $dom,
            $regTrib,
            'regEspTrib',
            $data->regEspTrib
        );

        /*
         * =========================================================
         * TOMADOR
         * =========================================================
         */

        $toma = $dom->createElement(
            'toma'
        );

        $infDps->appendChild($toma);

        /*
         * CPF ou CNPJ
         */
        if (!empty($data->cnpjTomador)) {

            $this->appendText(
                $dom,
                $toma,
                'CNPJ',
                $data->cnpjTomador
            );

        } elseif (!empty($data->cpfTomador)) {

            $this->appendText(
                $dom,
                $toma,
                'CPF',
                $data->cpfTomador
            );
        }

        $this->appendOptionalText(
            $dom,
            $toma,
            'xNome',
            $data->razaoTomador
        );

        /*
         * Endereço
         */
        $end = $dom->createElement(
            'end'
        );

        $toma->appendChild($end);

        $endNac = $dom->createElement(
            'endNac'
        );

        $end->appendChild($endNac);

        $this->appendOptionalText(
            $dom,
            $endNac,
            'cMun',
            $data->codigoMunicipioTomador
        );

        $this->appendOptionalText(
            $dom,
            $endNac,
            'CEP',
            $data->cepTomador
        );

        $this->appendOptionalText(
            $dom,
            $end,
            'xLgr',
            $data->logradouroTomador
        );

        $this->appendOptionalText(
            $dom,
            $end,
            'nro',
            $data->numeroTomador
        );

        $this->appendOptionalText(
            $dom,
            $end,
            'xCpl',
            $data->complementoTomador
        );

        $this->appendOptionalText(
            $dom,
            $end,
            'xBairro',
            $data->bairroTomador
        );

        $this->appendOptionalText(
            $dom,
            $toma,
            'fone',
            $data->foneTomador
        );

        $this->appendOptionalText(
            $dom,
            $toma,
            'email',
            $data->emailTomador
        );

        /*
         * =========================================================
         * SERVIÇO
         * =========================================================
         */

        $serv = $dom->createElement(
            'serv'
        );

        $infDps->appendChild($serv);

        /*
         * locPrest
         */
        $locPrest = $dom->createElement(
            'locPrest'
        );

        $serv->appendChild($locPrest);

        $this->appendText(
            $dom,
            $locPrest,
            'cLocPrestacao',
            $data->codigoMunicipioPrestacao ?? $data->codigoMunicipio
        );

        /*
         * cServ
         */
        $cServ = $dom->createElement(
            'cServ'
        );

        $serv->appendChild($cServ);

        $this->appendOptionalText(
            $dom,
            $cServ,
            'cTribNac',
            $data->codigoTributacaoNacional
        );

        $this->appendOptionalText(
            $dom,
            $cServ,
            'cTribMun',
            $data->codigoServicoMunicipal
        );

        $this->appendOptionalText(
            $dom,
            $cServ,
            'xDescServ',
            $data->descricaoServico
        );

        $this->appendOptionalText(
            $dom,
            $cServ,
            'cNBS',
            $data->codigoNbs
        );

        /*
         * infoCompl
         */
        if (!empty($data->informacaoComplementar)) {

            $infoCompl = $dom->createElementNS(
                self::NS_NFSE,
                'infoCompl'
            );

            $serv->appendChild($infoCompl);

            $this->appendText(
                $dom,
                $infoCompl,
                'xInfComp',
                $data->informacaoComplementar
            );
        }

        /*
         * =========================================================
         * VALORES
         * =========================================================
         */

        $valores = $dom->createElement(
            'valores'
        );

        $infDps->appendChild($valores);

        /*
         * vServPrest
         */
        $vServPrest = $dom->createElement(
            'vServPrest'
        );

        $valores->appendChild($vServPrest);

        $this->appendText(
            $dom,
            $vServPrest,
            'vServ',
            $this->decimal($data->valorServico)
        );

        /*
         * trib
         */
        $trib = $dom->createElement(
            'trib'
        );

        $valores->appendChild($trib);

        /*
         * =========================================================
         * TRIBUTAÇÃO MUNICIPAL
         * =========================================================
         */

        $tribMun = $dom->createElement(
            'tribMun'
        );

        $trib->appendChild($tribMun);

        $this->appendOptionalText(
            $dom,
            $tribMun,
            'tribISSQN',
            $data->tributaIss
        );

        $this->appendOptionalText(
            $dom,
            $tribMun,
            'tpRetISSQN',
            $data->tipoRetencaoIss
        );

        $this->appendOptionalText(
            $dom,
            $tribMun,
            'pAliq',
            $this->decimal($data->aliquotaIss)
        );

        /*
         * =========================================================
         * TRIBUTAÇÃO FEDERAL
         * =========================================================
         */

        $tribFed = $dom->createElement(
            'tribFed'
        );

        $trib->appendChild($tribFed);

        /*
         * PIS/COFINS
         */
        $piscofins = $dom->createElement(
            'piscofins'
        );

        $tribFed->appendChild($piscofins);

        $this->appendOptionalText(
            $dom,
            $piscofins,
            'CST',
            $data->cstPisCofins
        );

        $this->appendOptionalText(
            $dom,
            $piscofins,
            'tpRetPisCofins',
            $data->tipoRetencaoPisCofins
        );

        /*
         * Retenções
         */
        $this->appendOptionalText(
            $dom,
            $tribFed,
            'vRetCP',
            $this->decimal($data->valorRetencaoCp)
        );

        $this->appendOptionalText(
            $dom,
            $tribFed,
            'vRetIRRF',
            $this->decimal($data->valorRetencaoIrrf)
        );

        /*$this->appendOptionalText(
            $dom,
            $tribFed,
            'vRetCSLL',
            $this->decimal($data->valorRetencaoCsll)
        );*/

        /*
         * =========================================================
         * TOTAL TRIBUTOS
         * =========================================================
         */

        $totTrib = $dom->createElement(
            'totTrib'
        );

        $trib->appendChild($totTrib);

        $this->appendOptionalText(
            $dom,
            $totTrib,
            'pTotTribSN',
            $this->decimal($data->percentualTotalTributos)
        );

        /*
         * =========================================================
         * IBS / CBS
         * =========================================================
         */

        $ibscbs = $dom->createElement(
            'IBSCBS'
        );

        $infDps->appendChild($ibscbs);

        $this->appendOptionalText(
            $dom,
            $ibscbs,
            'finNFSe',
            $data->finNfse
        );

        /*$this->appendOptionalText(
            $dom,
            $ibscbs,
            'indFinal',
            $data->indFinal
        );*/

        $this->appendOptionalText(
            $dom,
            $ibscbs,
            'cIndOp',
            $data->cIndOp
        );

        $this->appendOptionalText(
            $dom,
            $ibscbs,
            'indDest',
            $data->indDest
        );

        /*
         * valores
         */
        $ibscbsValores = $dom->createElement(
            'valores'
        );

        $ibscbs->appendChild($ibscbsValores);

        /*
         * trib
         */
        $ibscbsTrib = $dom->createElement(
            'trib'
        );

        $ibscbsValores->appendChild($ibscbsTrib);

        /*
         * gIBSCBS
         */
        $gibscbs = $dom->createElement(
            'gIBSCBS'
        );

        $ibscbsTrib->appendChild($gibscbs);

        $this->appendOptionalText(
            $dom,
            $gibscbs,
            'CST',
            $data->cstIbsCbs
        );

        $this->appendOptionalText(
            $dom,
            $gibscbs,
            'cClassTrib',
            $data->cClassTrib
        );

        return $dom->saveXML($dps);
    }

    /**
     * Gera:
     *
     * DPS + cMun + tipo inscrição + inscrição federal + série + nDPS
     */
    private function generateId(DPSDataSnDTO $data): string
    {
        $municipio = preg_replace(
            '/\D/',
            '',
            (string) $data->codigoMunicipio
        );

        $serie = preg_replace(
            '/\D/',
            '',
            (string) $data->serie
        );

        /*
         * 1 = CNPJ
         * 2 = CPF
         */
        if (!empty($data->cnpjPrestador)) {

            $tipoInscricao = '1';

            $documento = preg_replace(
                '/\D/',
                '',
                $data->cnpjPrestador
            );

        } else {

            $tipoInscricao = '2';

            $documento = preg_replace(
                '/\D/',
                '',
                $data->cpfPrestador
            );
        }

        $municipio = str_pad(
            $municipio,
            7,
            '0',
            STR_PAD_LEFT
        );

        $documento = str_pad(
            $documento,
            14,
            '0',
            STR_PAD_LEFT
        );

        $serie = str_pad(
            $serie,
            5,
            '0',
            STR_PAD_LEFT
        );

        $numero = str_pad(
            (string) $data->numDps,
            15,
            '0',
            STR_PAD_LEFT
        );

        return 'DPS'
            . $municipio
            . $tipoInscricao
            . $documento
            . $serie
            . $numero;
    }

    /**
     * Adiciona elemento obrigatório.
     */
    private function appendText(
        DOMDocument $dom,
        DOMElement $parent,
        string $name,
        mixed $value
    ): DOMElement {

        if ($value === null || $value === '') {
            throw new InvalidArgumentException(
                "O campo {$name} é obrigatório."
            );
        }

        $element = $dom->createElement(
            $name
        );

        $element->appendChild(
            $dom->createTextNode((string) $value)
        );

        $parent->appendChild($element);

        return $element;
    }

    /**
     * Adiciona elemento somente quando possui valor.
     */
    private function appendOptionalText(
        DOMDocument $dom,
        DOMElement $parent,
        string $name,
        mixed $value
    ): ?DOMElement {

        if ($value === null || $value === '') {
            return null;
        }

        $element = $dom->createElement(
            $name
        );

        $element->appendChild(
            $dom->createTextNode((string) $value)
        );

        $parent->appendChild($element);

        return $element;
    }

    /**
     * Normaliza valores decimais para o padrão XML.
     *
     * Ex:
     * 10       => 10.00
     * 10,5     => 10.50
     * 10.5     => 10.50
     */
    private function decimal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = str_replace(
            ',',
            '.',
            (string) $value
        );

        return number_format(
            (float) $value,
            2,
            '.',
            ''
        );
    }
}