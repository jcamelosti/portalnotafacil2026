<?php

namespace App\Services\Nfse;

use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use RuntimeException;

class DanfseXmlParser
{
    private DOMXPath $xpath;

    public function parse(string $xml): array
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        if (! $dom->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOCDATA)) {
            throw new RuntimeException('XML da NFS-e inválido.');
        }

        $this->xpath = new DOMXPath($dom);
        $this->xpath->registerNamespace('n', 'http://www.sped.fazenda.gov.br/nfse');

        $nf = $this->node('//n:infNFSe');
        $dps = $this->node('//n:infDPS');

        if (! $nf || ! $dps) {
            throw new RuntimeException('Não foi encontrado infNFSe/infDPS no XML.');
        }

        $nfId = $nf->getAttribute('Id');
        $accessKey = preg_replace('/^NFS/', '', $nfId);

        $vServico = $this->value($dps, 'n:valores/n:vServPrest/n:vServ');
        $vBaseIbsCbs = $this->value($nf, 'n:IBSCBS/n:valores/n:vBC');

        return [
            'header' => [
                'chave' => $accessKey,
                'municipio' => $this->value($nf, 'n:xLocEmi'),
                'ambiente_gerador' => $this->value($nf, 'n:ambGer'),
                'tipo_ambiente' => $this->value($dps, 'n:tpAmb'),
                'numero_nfse' => $this->value($nf, 'n:nNFSe'),
                'competencia' => $this->date($this->value($dps, 'n:dCompet'), 'd/m/Y'),
                'emissao_nfse' => $this->dateTime($this->value($nf, 'n:dhProc')),
                'numero_dps' => $this->value($dps, 'n:nDPS'),
                'serie_dps' => $this->value($dps, 'n:serie'),
                'emissao_dps' => $this->dateTime($this->value($dps, 'n:dhEmi')),
                'situacao' => $this->mapTpEmis($this->value($nf, 'n:tpEmis')),
                'finalidade' => $this->mapStatus($this->value($nf, 'n:cStat')),
            ],

            'prestador' => [
                'cnpj' => $this->value($nf, 'n:emit/n:CNPJ'),
                'im' => $this->value($nf, 'n:emit/n:IM'),
                'nome' => $this->value($nf, 'n:emit/n:xNome'),
                'fantasia' => $this->value($nf, 'n:emit/n:xFant'),
                'endereco' => trim(implode(', ', array_filter([
                    $this->value($nf, 'n:emit/n:enderNac/n:xLgr'),
                    $this->value($nf, 'n:emit/n:enderNac/n:nro'),
                    $this->value($nf, 'n:emit/n:enderNac/n:xCpl'),
                ]))),
                'bairro' => $this->value($nf, 'n:emit/n:enderNac/n:xBairro'),
                'municipio' => $this->value($nf, 'n:xLocEmi'),
                'uf' => $this->value($nf, 'n:emit/n:enderNac/n:UF'),
                'cep' => $this->value($nf, 'n:emit/n:enderNac/n:CEP'),
                'fone' => $this->value($nf, 'n:emit/n:fone'),
                'email' => $this->value($nf, 'n:emit/n:email'),
                'op_simples' => $this->mapSimples($this->value($dps, 'n:prest/n:regTrib/n:opSimpNac')),
                'reg_apuracao' => $this->mapApuracao($this->value($dps, 'n:prest/n:regTrib/n:regApTribSN')),
                'regime_especial' => $this->mapRegimeEspecial($this->value($dps, 'n:prest/n:regTrib/n:regEspTrib')),
            ],

            'tomador' => [
                'cnpj' => $this->value($dps, 'n:toma/n:CNPJ'),
                'nome' => $this->value($dps, 'n:toma/n:xNome'),
                'endereco' => trim(implode(', ', array_filter([
                    $this->value($dps, 'n:toma/n:end/n:xLgr'),
                    $this->value($dps, 'n:toma/n:end/n:nro'),
                    $this->value($dps, 'n:toma/n:end/n:xCpl'),
                ]))),
                'bairro' => $this->value($dps, 'n:toma/n:end/n:xBairro'),
                'municipio' => $this->municipioNome($this->value($dps, 'n:toma/n:end/n:endNac/n:cMun')),
                'uf' => $this->ufDoCodigoMunicipio($this->value($dps, 'n:toma/n:end/n:endNac/n:cMun')),
                'cep' => $this->value($dps, 'n:toma/n:end/n:endNac/n:CEP'),
                'fone' => $this->value($dps, 'n:toma/n:fone'),
                'email' => $this->value($dps, 'n:toma/n:email'),
                'destinatario_proprio' => true,
            ],

            'servico' => [
                'codigo_tributacao_nacional' => $this->value($dps, 'n:serv/n:cServ/n:cTribNac'),
                'codigo_tributacao_municipal' => $this->value($dps, 'n:serv/n:cServ/n:cTribMun'),
                'descricao_tributacao_nacional' => $this->value($nf, 'n:xTribNac'),
                'descricao_tributacao_municipal' => $this->value($nf, 'n:xTribMun'),
                'nbs' => $this->value($dps, 'n:serv/n:cServ/n:cNBS'),
                'descricao_nbs' => $this->value($nf, 'n:xNBS'),
                'local_prestacao' => $this->value($nf, 'n:xLocPrestacao'),
                'uf_prestacao' => $this->value($nf, 'n:emit/n:enderNac/n:UF'),
                'pais' => 'Brasil',
                'descricao' => $this->value($dps, 'n:serv/n:cServ/n:xDescServ'),
            ],

            'municipal' => [
                'regime_especial' => $this->mapRegimeEspecial($this->value($dps, 'n:prest/n:regTrib/n:regEspTrib')),
                'tipo_tributacao' => $this->mapTribIssqn($this->value($dps, 'n:valores/n:trib/n:tribMun/n:tribISSQN')),
                'tipo_imunidade' => '-',
                'base_calculo' => $this->money($this->value($nf, 'n:valores/n:vBC')),
                'aliquota' => $this->percent($this->value($dps, 'n:valores/n:trib/n:tribMun/n:pAliq')),
                'retencao' => $this->mapRetIssqn($this->value($dps, 'n:valores/n:trib/n:tribMun/n:tpRetISSQN')),
                'suspensao' => '-',
                'numero_processo_suspensao' => '-',
                'issqn_apurado' => $this->money($this->value($nf, 'n:valores/n:vISSQN')),
            ],

            'federal' => [
                'irrf' => '-',
                'contribuicao_previdenciaria' => '-',
                'csll' => '-',
                'pis_debito' => '-',
                'cofins_debito' => '-',
                'pis_cofins_csll' => $this->mapPisCofins($this->value($dps, 'n:valores/n:trib/n:tribFed/n:piscofins/n:tpRetPisCofins')),
            ],

            'ibs_cbs' => [
                'cst' => $this->value($dps, 'n:IBSCBS/n:valores/n:trib/n:gIBSCBS/n:CST', '-'),
                'cclasstrib' => $this->value($dps, 'n:IBSCBS/n:valores/n:trib/n:gIBSCBS/n:cClassTrib', '-'),
                'indicador_operacao' => $this->value($dps, 'n:IBSCBS/n:cIndOp', '-'),
                'codigo_ibge_incidencia' => $this->value($nf, 'n:IBSCBS/n:cLocalidadeIncid', '-'),
                'municipio_incidencia' => $this->value($nf, 'n:IBSCBS/n:xLocalidadeIncid', '-'),
                'base_original' => $this->money($vServico),
                'exclusoes_reducoes' => $this->money(max(0, (float)$vServico - (float)$vBaseIbsCbs)),
                'base_calculo' => $this->money($vBaseIbsCbs),
                'reducao_uf' => $this->percent($this->value($nf, 'n:IBSCBS/n:valores/n:uf/n:pRedAliqUF')),
                'reducao_mun' => $this->percent($this->value($nf, 'n:IBSCBS/n:valores/n:mun/n:pRedAliqMun')),
                'reducao_cbs' => $this->percent($this->value($nf, 'n:IBSCBS/n:valores/n:fed/n:pRedAliqCBS')),
                'aliquota_uf' => $this->percent($this->value($nf, 'n:IBSCBS/n:valores/n:uf/n:pAliqEfetUF')),
                'aliquota_mun' => $this->percent($this->value($nf, 'n:IBSCBS/n:valores/n:mun/n:pAliqEfetMun')),
                'aliquota_cbs' => $this->percent($this->value($nf, 'n:IBSCBS/n:valores/n:fed/n:pAliqEfetCBS')),
                'ibs_uf' => $this->money($this->value($nf, 'n:IBSCBS/n:totCIBS/n:gIBS/n:gIBSUFTot/n:vIBSUF')),
                'ibs_mun' => $this->money($this->value($nf, 'n:IBSCBS/n:totCIBS/n:gIBS/n:gIBSMunTot/n:vIBSMun')),
                'ibs_total' => $this->money($this->value($nf, 'n:IBSCBS/n:totCIBS/n:gIBS/n:vIBSTot')),
                'cbs' => $this->money($this->value($nf, 'n:IBSCBS/n:totCIBS/n:gCBS/n:vCBS')),
                'total' => $this->money(
                    (float)$this->value($nf, 'n:IBSCBS/n:totCIBS/n:gIBS/n:vIBSTot')
                    + (float)$this->value($nf, 'n:IBSCBS/n:totCIBS/n:gCBS/n:vCBS')
                ),
            ],

            'totais' => [
                'operacao' => $this->money($vServico),
                'desconto_incondicionado' => '-',
                'desconto_condicionado' => '-',
                'retencoes' => $this->money($this->value($nf, 'n:valores/n:vTotalRet')),
                'liquido_nfse' => $this->money($this->value($nf, 'n:valores/n:vLiq')),
                'total_ibs_cbs' => $this->money(
                    (float)$this->value($nf, 'n:IBSCBS/n:totCIBS/n:gIBS/n:vIBSTot')
                    + (float)$this->value($nf, 'n:IBSCBS/n:totCIBS/n:gCBS/n:vCBS')
                ),
                'liquido_com_ibs_cbs' => $this->money($this->value($nf, 'n:IBSCBS/n:totCIBS/n:vTotNF')),
            ],

            //'informacoes_complementares' => $this->value($nf, 'n:xOutInf'),
            'informacoes_complementares' => $this->value($dps, 'n:serv/n:infoCompl/n:xInfComp'),
        ];
    }

    
    public function parseDFe(string $xml): array
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->resolveExternals = false;
        $dom->substituteEntities = false;

        $previous = libxml_use_internal_errors(true);

        try {
            if (!$dom->loadXML(
                $xml,
                LIBXML_NOBLANKS | LIBXML_NOCDATA | LIBXML_NONET
            )) {
                throw new RuntimeException('XML da NFS-e inválido.');
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        // Remove a assinatura digital integralmente.
        $signatures = $dom->getElementsByTagNameNS(
            'http://www.w3.org/2000/09/xmldsig#',
            'Signature'
        );

        for ($i = $signatures->length - 1; $i >= 0; $i--) {
            $signature = $signatures->item($i);

            if ($signature && $signature->parentNode) {
                $signature->parentNode->removeChild($signature);
            }
        }

        $this->xpath = new DOMXPath($dom);

        $this->xpath->registerNamespace(
            'n',
            'http://www.sped.fazenda.gov.br/nfse'
        );

        $nf = $this->node('//n:infNFSe');
        $dps = $this->node('//n:infDPS');

        if (!$nf || !$dps) {
            throw new RuntimeException(
                'Não foi encontrado infNFSe/infDPS no XML.'
            );
        }

        $nfId = $nf->getAttribute('Id');
        $accessKey = preg_replace('/^NFS/', '', $nfId);

        $vServico = $this->value(
            $dps,
            'n:valores/n:vServPrest/n:vServ'
        );

        $vBaseIbsCbs = $this->value(
            $nf,
            'n:IBSCBS/n:valores/n:vBC'
        );

        $vServicoNumero = (float) ($vServico ?? 0);
        $vBaseIbsNumero = (float) ($vBaseIbsCbs ?? 0);

        $vIbsTotal = $this->value(
            $nf,
            'n:IBSCBS/n:totCIBS/n:gIBS/n:vIBSTot'
        );

        $vCbs = $this->value(
            $nf,
            'n:IBSCBS/n:totCIBS/n:gCBS/n:vCBS'
        );

        $vTotalIbsCbs = (float) ($vIbsTotal ?? 0)
            + (float) ($vCbs ?? 0);

        $cMunPrestador = $this->value(
            $nf,
            'n:emit/n:enderNac/n:cMun'
        );

        $cMunTomador = $this->value(
            $dps,
            'n:toma/n:end/n:endNac/n:cMun'
        );

        $cMunIncidencia = $this->value($nf, 'n:cLocIncid', '-');
        $xMunIncidencia = $this->value($nf, 'n:xLocIncid', '-');

        $enderecoPrestador = trim(implode(', ', array_filter([
            $this->value($nf, 'n:emit/n:enderNac/n:xLgr'),
            $this->value($nf, 'n:emit/n:enderNac/n:nro'),
            $this->value($nf, 'n:emit/n:enderNac/n:xCpl'),
        ], fn ($v) => $v !== null && $v !== '')));

        $enderecoTomador = trim(implode(', ', array_filter([
            $this->value($dps, 'n:toma/n:end/n:xLgr'),
            $this->value($dps, 'n:toma/n:end/n:nro'),
            $this->value($dps, 'n:toma/n:end/n:xCpl'),
        ], fn ($v) => $v !== null && $v !== '')));

        return [
            'header' => [
                'chave' => $accessKey,
                'municipio' => $this->value($nf, 'n:xLocEmi'),
                'ambiente_gerador' => $this->value($nf, 'n:ambGer'),
                'tipo_ambiente' => $this->value($dps, 'n:tpAmb'),
                'numero_nfse' => $this->value($nf, 'n:nNFSe'),
                'competencia' => $this->date(
                    $this->value($dps, 'n:dCompet'),
                    'd/m/Y'
                ),
                'emissao_nfse' => $this->dateTime(
                    $this->value($nf, 'n:dhProc')
                ),
                'numero_dps' => $this->value($dps, 'n:nDPS'),
                'serie_dps' => $this->value($dps, 'n:serie'),
                'emissao_dps' => $this->dateTime(
                    $this->value($dps, 'n:dhEmi')
                ),
                'situacao' => $this->mapTpEmis(
                    $this->value($nf, 'n:tpEmis')
                ),
                'finalidade' => $this->mapStatus(
                    $this->value($nf, 'n:cStat')
                ),
            ],

            'prestador' => [
                'cnpj' => $this->value($nf, 'n:emit/n:CNPJ'),
                'im' => $this->value($nf, 'n:emit/n:IM'),
                'nome' => $this->value($nf, 'n:emit/n:xNome'),
                'fantasia' => $this->value($nf, 'n:emit/n:xFant'),
                'endereco' => $enderecoPrestador,
                'bairro' => $this->value(
                    $nf,
                    'n:emit/n:enderNac/n:xBairro'
                ),
                'municipio' => $this->municipioNome($cMunPrestador),
                'uf' => $this->value($nf, 'n:emit/n:enderNac/n:UF'),
                'cep' => $this->value($nf, 'n:emit/n:enderNac/n:CEP'),
                'fone' => $this->value($nf, 'n:emit/n:fone'),
                'email' => $this->value($nf, 'n:emit/n:email'),
                'op_simples' => $this->mapSimples(
                    $this->value($dps, 'n:prest/n:regTrib/n:opSimpNac')
                ),
                'reg_apuracao' => $this->mapApuracao(
                    $this->value($dps, 'n:prest/n:regTrib/n:regApTribSN')
                ),
                'regime_especial' => $this->mapRegimeEspecial(
                    $this->value($dps, 'n:prest/n:regTrib/n:regEspTrib')
                ),
            ],

            'tomador' => [
                'cnpj' => $this->value($dps, 'n:toma/n:CNPJ')
                    ?? $this->value($dps, 'n:toma/n:CPF'),
                'nome' => $this->value($dps, 'n:toma/n:xNome'),
                'endereco' => $enderecoTomador,
                'bairro' => $this->value(
                    $dps,
                    'n:toma/n:end/n:xBairro'
                ),
                'municipio' => $this->municipioNome($cMunTomador),
                'uf' => $this->ufDoCodigoMunicipio($cMunTomador),
                'cep' => $this->value(
                    $dps,
                    'n:toma/n:end/n:endNac/n:CEP'
                ),
                'fone' => $this->value($dps, 'n:toma/n:fone'),
                'email' => $this->value($dps, 'n:toma/n:email'),
                'destinatario_proprio' => true,
            ],

            'servico' => [
                'codigo_tributacao_nacional' => $this->value(
                    $dps,
                    'n:serv/n:cServ/n:cTribNac'
                ),
                'codigo_tributacao_municipal' => $this->value(
                    $dps,
                    'n:serv/n:cServ/n:cTribMun'
                ),
                'descricao_tributacao_nacional' => $this->value(
                    $nf,
                    'n:xTribNac'
                ),
                'descricao_tributacao_municipal' => $this->value(
                    $nf,
                    'n:xTribMun'
                ),
                'nbs' => $this->value(
                    $dps,
                    'n:serv/n:cServ/n:cNBS'
                ),
                'descricao_nbs' => $this->value($nf, 'n:xNBS'),
                'local_prestacao' => $this->value(
                    $nf,
                    'n:xLocPrestacao'
                ),
                'uf_prestacao' => $this->value(
                    $nf,
                    'n:emit/n:enderNac/n:UF'
                ),
                'pais' => 'Brasil',
                'descricao' => $this->value(
                    $dps,
                    'n:serv/n:cServ/n:xDescServ'
                ),
            ],

            'municipal' => [
                'regime_especial' => $this->mapRegimeEspecial(
                    $this->value($dps, 'n:prest/n:regTrib/n:regEspTrib')
                ),
                'tipo_tributacao' => $this->mapTribIssqn(
                    $this->value(
                        $dps,
                        'n:valores/n:trib/n:tribMun/n:tribISSQN'
                    )
                ),
                'tipo_imunidade' => '-',
                'base_calculo' => $this->money(
                    $this->value($nf, 'n:valores/n:vBC')
                ),
                'aliquota' => $this->percent(
                    $this->value(
                        $dps,
                        'n:valores/n:trib/n:tribMun/n:pAliq'
                    )
                ),
                'retencao' => $this->mapRetIssqn(
                    $this->value(
                        $dps,
                        'n:valores/n:trib/n:tribMun/n:tpRetISSQN'
                    )
                ),
                'suspensao' => '-',
                'numero_processo_suspensao' => '-',
                'issqn_apurado' => $this->money(
                    $this->value($nf, 'n:valores/n:vISSQN')
                ),
            ],

            'federal' => [
                'irrf' => '-',
                'contribuicao_previdenciaria' => '-',
                'csll' => '-',
                'pis_debito' => '-',
                'cofins_debito' => '-',
                'pis_cofins_csll' => $this->mapPisCofins(
                    $this->value(
                        $dps,
                        'n:valores/n:trib/n:tribFed/n:piscofins/n:tpRetPisCofins'
                    )
                ),
            ],

            'ibs_cbs' => [
                'cst' => $this->value(
                    $dps,
                    'n:IBSCBS/n:valores/n:trib/n:gIBSCBS/n:CST',
                    '-'
                ),
                'cclasstrib' => $this->value(
                    $dps,
                    'n:IBSCBS/n:valores/n:trib/n:gIBSCBS/n:cClassTrib',
                    '-'
                ),
                'indicador_operacao' => $this->value(
                    $dps,
                    'n:IBSCBS/n:cIndOp',
                    '-'
                ),
                'codigo_ibge_incidencia' => $cMunIncidencia,
                'municipio_incidencia' => $xMunIncidencia,
                'base_original' => $this->money($vServico),
                'exclusoes_reducoes' => $this->money(
                    max(0, $vServicoNumero - $vBaseIbsNumero)
                ),
                'base_calculo' => $this->money($vBaseIbsCbs),
                'reducao_uf' => $this->percent(
                    $this->value($nf, 'n:IBSCBS/n:valores/n:uf/n:pRedAliqUF')
                ),
                'reducao_mun' => $this->percent(
                    $this->value($nf, 'n:IBSCBS/n:valores/n:mun/n:pRedAliqMun')
                ),
                'reducao_cbs' => $this->percent(
                    $this->value($nf, 'n:IBSCBS/n:valores/n:fed/n:pRedAliqCBS')
                ),
                'aliquota_uf' => $this->percent(
                    $this->value($nf, 'n:IBSCBS/n:valores/n:uf/n:pAliqEfetUF')
                ),
                'aliquota_mun' => $this->percent(
                    $this->value($nf, 'n:IBSCBS/n:valores/n:mun/n:pAliqEfetMun')
                ),
                'aliquota_cbs' => $this->percent(
                    $this->value($nf, 'n:IBSCBS/n:valores/n:fed/n:pAliqEfetCBS')
                ),
                'ibs_uf' => $this->money(
                    $this->value(
                        $nf,
                        'n:IBSCBS/n:totCIBS/n:gIBS/n:gIBSUFTot/n:vIBSUF'
                    )
                ),
                'ibs_mun' => $this->money(
                    $this->value(
                        $nf,
                        'n:IBSCBS/n:totCIBS/n:gIBS/n:gIBSMunTot/n:vIBSMun'
                    )
                ),
                'ibs_total' => $this->money($vIbsTotal),
                'cbs' => $this->money($vCbs),
                'total' => $this->money($vTotalIbsCbs),
            ],

            'totais' => [
                'operacao' => $this->money($vServico),
                'desconto_incondicionado' => '-',
                'desconto_condicionado' => '-',
                'retencoes' => $this->money(
                    $this->value($nf, 'n:valores/n:vTotalRet')
                ),
                'liquido_nfse' => $this->money(
                    $this->value($nf, 'n:valores/n:vLiq')
                ),
                'total_ibs_cbs' => $this->money($vTotalIbsCbs),
                'liquido_com_ibs_cbs' => $this->money(
                    $this->value($nf, 'n:IBSCBS/n:totCIBS/n:vTotNF')
                ),
            ],

            'informacoes_complementares' => $this->value(
                $dps,
                'n:serv/n:infoCompl/n:xInfComp'
            ),
        ];
    }


    private function node(string $query): ?\DOMElement
    {
        $nodes = $this->xpath->query($query);
        return ($nodes && $nodes->length) ? $nodes->item(0) : null;
    }

    private function value(\DOMNode $context, string $query, string $default = ''): string
    {
        $nodes = $this->xpath->query($query, $context);
        if (! $nodes || ! $nodes->length) {
            return $default;
        }

        return trim($nodes->item(0)->textContent);
    }

    private function date(?string $value, string $format): string
    {
        return $value ? Carbon::parse($value)->format($format) : '-';
    }

    private function dateTime(?string $value): string
    {
        return $value ? Carbon::parse($value)->format('d/m/Y H:i:s') : '-';
    }

    private function money(?string $value): string
    {
        return $value === null || $value === '' ? '-' : 'R$ ' . number_format((float)$value, 2, ',', '.');
    }

    private function percent(?string $value): string
    {
        return $value === null || $value === '' ? '-' : number_format((float)$value, 2, ',', '.') . '%';
    }

    private function mapTpEmis(?string $value): string
    {
        return match ($value) {
            '1', '2' => 'NFS-e gerada',
            default => $value ?: '-',
        };
    }

    private function mapStatus(?string $value): string
    {
        return match ($value) {
            '100' => 'NFS-e regular',
            default => $value ?: '-',
        };
    }

    private function mapSimples(?string $value): string
    {
        return match ($value) {
            '1', '3' => 'Optante - Microempresa ou Empresa',
            default => $value ?: '-',
        };
    }

    private function mapApuracao(?string $value): string
    {
        return match ($value) {
            '1' => 'Regime de Apuração Tributária pelo SN',
            default => $value ?: '-',
        };
    }

    private function mapRegimeEspecial(?string $value): string
    {
        return match ($value) {
            '0' => 'Nenhum',
            default => $value ?: '-',
        };
    }

    private function mapTribIssqn(?string $value): string
    {
        return match ($value) {
            '1' => 'Operação tributável',
            default => $value ?: '-',
        };
    }

    private function mapRetIssqn(?string $value): string
    {
        return match ($value) {
            '1' => 'Não Retido',
            default => $value ?: '-',
        };
    }

    private function mapPisCofins(?string $value): string
    {
        return match ($value) {
            '0' => 'PIS/COFINS/CSLL Não Retidos',
            default => $value ?: '-',
        };
    }

    /**
     * O XML traz apenas o código IBGE do município do tomador.
     * Para não inventar o nome, usamos o nome do local de incidência
     * quando disponível no XML. Em produção, substitua por seu cadastro IBGE.
     */
    private function municipioNome(?string $codigo): string
    {
        return $codigo ?: '-';
    }

    private function ufDoCodigoMunicipio(?string $codigo): string
    {
        if (! $codigo || strlen($codigo) < 2) {
            return '-';
        }

        $uf = substr($codigo, 0, 2);

        return [
            '11'=>'RO','12'=>'AC','13'=>'AM','14'=>'RR','15'=>'PA','16'=>'AP','17'=>'TO',
            '21'=>'MA','22'=>'PI','23'=>'CE','24'=>'RN','25'=>'PB','26'=>'PE','27'=>'AL',
            '28'=>'SE','29'=>'BA','31'=>'MG','32'=>'ES','33'=>'RJ','35'=>'SP','41'=>'PR',
            '42'=>'SC','43'=>'RS','50'=>'MS','51'=>'MT','52'=>'GO','53'=>'DF',
        ][$uf] ?? '-';
    }
}
