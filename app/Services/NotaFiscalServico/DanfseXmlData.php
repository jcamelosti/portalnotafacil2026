<?php

declare(strict_types=1);

namespace App\Services\NotaFiscalServico;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use InvalidArgumentException;

final class DanfseXmlData
{
    /** @var array<string, string>|null */
    private static ?array $municipalities = null;

    /**
     * @return array<string, mixed>
     */
    public static function fromXml(string $xml, bool $cancelled = false, bool $substituted = false): array
    {
        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);

        try {
            if (!$document->loadXML($xml)) {
                throw new InvalidArgumentException('XML da NFS-e inválido.');
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        $xpath = new DOMXPath($document);
        $infNfse = self::firstElement($xpath, $document, 'infNFSe');
        $dps = self::firstElement($xpath, $document, 'DPS');
        $prest = self::firstElement($xpath, $dps, 'prest');
        $emit = self::firstElement($xpath, $infNfse, 'emit');
        $enderNac = self::firstElement($xpath, $emit, 'enderNac');
        $prestAddress = self::firstElement($xpath, $prest, 'end') ?: $enderNac;
        $regTrib = self::firstElement($xpath, $prest, 'regTrib');
        $serv = self::firstElement($xpath, $dps, 'serv');
        $valores = self::childElement($xpath, $infNfse, 'valores');
        $toma = self::firstElement($xpath, $dps, 'toma');
        $intermed = self::firstElement($xpath, $dps, 'interm');
        $declaredIbsCbs = self::firstElement($xpath, $dps, 'IBSCBS');
        $calculatedIbsCbs = self::childElement($xpath, $infNfse, 'IBSCBS');
        $ibsCbs = $calculatedIbsCbs ?: $declaredIbsCbs;
        $recipient = self::firstElement($xpath, $declaredIbsCbs ?: $dps, 'dest');
        $tribFed = self::firstElement($xpath, $dps, 'tribFed');
        $piscofins = self::firstElement($xpath, $tribFed, 'piscofins');

        if (!$infNfse || !$dps || !$emit || !$serv || !$valores) {
            throw new InvalidArgumentException('XML da NFS-e não contém os grupos obrigatórios do DANFSe.');
        }

        [$compet] = self::formatDateTime(self::text($xpath, $dps, 'dCompet'));
        [$nfseDate, $nfseTime] = self::formatDateTime(self::text($xpath, $infNfse, 'dhProc'));
        [$dpsDate, $dpsTime] = self::formatDateTime(self::text($xpath, $dps, 'dhEmi'));

        $opSimpNac = self::text($xpath, $regTrib, 'opSimpNac');
        $regApTrib = self::text($xpath, $regTrib, 'regApTribSN');
        $regEspTrib = self::text($xpath, $regTrib, 'regEspTrib');
        $issqnRetentionType = self::text($xpath, $dps, 'tpRetISSQN');
        $issqnTypeForCalculation = $issqnRetentionType ?: '1';
        $issqnValue = self::text($xpath, $valores, 'vISSQN');
        $issqnRetained = in_array($issqnTypeForCalculation, ['2', '3'], true) ? self::number($issqnValue) : 0.0;
        $totalRetentions = self::text($xpath, $valores, 'vTotalRet');
        $totalFederalRetentions = $totalRetentions !== '' ? max(0.0, self::number($totalRetentions) - $issqnRetained) : 0.0;
        $description = self::text($xpath, $serv, 'xDescServ');
        $nationalTax = self::text($xpath, $serv, 'cTribNac');
        $key = self::nfseKey($infNfse);
        $vRetCp = self::text($xpath, $tribFed, 'vRetCP');
        $vRetCsll = self::text($xpath, $tribFed, 'vRetCSLL');
        $pis = self::text($xpath, $piscofins, 'vPis');
        $cofins = self::text($xpath, $piscofins, 'vCofins');
        $pisCofinsRetentionType = self::text($xpath, $piscofins, 'tpRetPisCofins');
        $usesLegacyPisCofinsRetention = $pisCofinsRetentionType === '1';
        $pisCofinsDebit = $usesLegacyPisCofinsRetention ? 0.0 : self::number($pis) + self::number($cofins);
        $issuerMunicipalityCode = self::text($xpath, $prestAddress, 'cMun');
        $issuerCity = self::text($xpath, $prest, 'xMun')
            ?: self::text($xpath, $prest, 'xCidade')
            ?: self::text($xpath, $infNfse, 'xLocEmi')
            ?: self::municipalityName($issuerMunicipalityCode)
            ?: $issuerMunicipalityCode;
        $issuerUf = self::text($xpath, $prest, 'UF')
            ?: self::text($xpath, $emit, 'UF')
            ?: self::ufFromIbgeCode($issuerMunicipalityCode);
        $issuerCityState = self::cityState($issuerCity, $issuerUf);
        $serviceLocationCode = self::text($xpath, $serv, 'cLocPrestacao');
        $serviceCountry = self::text($xpath, $serv, 'cPaisPrestacao') ?: ($serviceLocationCode !== '' ? 'BR' : '');
        $serviceCity = self::text($xpath, $infNfse, 'xLocPrestacao')
            ?: self::municipalityName($serviceLocationCode)
            ?: $serviceLocationCode;
        $serviceUf = self::ufFromIbgeCode($serviceLocationCode) ?: $issuerUf;
        $incidenceCode = self::text($xpath, $infNfse, 'cLocIncid');
        $incidenceUf = self::ufFromIbgeCode($incidenceCode) ?: $issuerUf;
        $issqnTaxType = self::text($xpath, $dps, 'tribISSQN');
        $substitutedKey = self::text($xpath, $dps, 'chSubstda');
        $substituted = $substituted || $substitutedKey !== '';
        $competYear = (int) substr(self::text($xpath, $dps, 'dCompet'), 0, 4);
        $federalTaxBlockPrinted = $competYear === 0 || $competYear <= 2026;
        $totalIbsCbs = self::sumCurrencyValues(
            self::text($xpath, $ibsCbs, 'vIBSTot'),
            self::text($xpath, $ibsCbs, 'vCBS'),
        );

        $data = [
            'cancelled' => $cancelled,
            'substituted' => $substituted,
            'environment' => self::text($xpath, $dps, 'tpAmb'),
            'environment_notice' => self::environmentNotice(self::text($xpath, $dps, 'tpAmb')),
            'municipality_environment' => str_starts_with($nationalTax, '99') ? '' : 'Município: ' . $issuerCityState,
            'environment_generator' => self::environmentGenerator(self::text($xpath, $infNfse, 'ambGer')),
            'environment_type' => self::environmentType(self::text($xpath, $dps, 'tpAmb')),
            'key_nfse' => $key,
            'qr_code_url' => 'https://www.nfse.gov.br/ConsultaPublica/?tpc=1&chave=' . $key,
            'nfse_number' => self::text($xpath, $infNfse, 'nNFSe'),
            'compet' => $compet,
            'dt_nfse' => $nfseDate,
            'hr_nfse' => $nfseTime,
            'dt_dps' => $dpsDate,
            'hr_dps' => $dpsTime,
            'dps_number' => self::text($xpath, $dps, 'nDPS'),
            'dps_serie' => self::text($xpath, $dps, 'serie'),
            'issuer_type' => self::issuerType(self::text($xpath, $dps, 'tpEmit')),
            'nfse_status' => self::nfseStatus(self::text($xpath, $infNfse, 'cStat'), $cancelled, $substituted),
            'purpose' => self::purpose(self::text($xpath, $declaredIbsCbs, 'finNFSe')),
            'watermark' => self::watermark($cancelled, $substituted),
            'issuer' => [
                'id' => self::taxIdentifier($xpath, $prest, $emit),
                'municipal_registration' => self::text($xpath, $prest, 'IM') ?: self::text($xpath, $emit, 'IM') ?: '-',
                'phone' => self::formatPhone(self::text($xpath, $prest, 'fone') ?: self::text($xpath, $emit, 'fone')) ?: '-',
                'name' => self::text($xpath, $prest, 'xNome') ?: self::text($xpath, $emit, 'xNome') ?: self::text($xpath, $emit, 'xFant') ?: '-',
                'email' => self::text($xpath, $prest, 'email') ?: self::text($xpath, $emit, 'email') ?: '-',
                'address' => self::formatAddress($xpath, $prestAddress),
                'city' => trim($issuerCity . ' - ' . $issuerUf, ' -') ?: '-',
                'city_state' => $issuerCityState,
                'city_code_cep' => self::codeCep(self::text($xpath, $prestAddress, 'cMun'), self::text($xpath, $prestAddress, 'CEP')),
                'cep' => self::formatCep(self::text($xpath, $prestAddress, 'CEP')),
                'simples' => self::simplesNacional($opSimpNac),
                'tax_regim' => $opSimpNac === '3' ? self::taxRegime($regApTrib) : '-',
            ],
            'taker' => self::personData($xpath, $toma),
            'recipient' => self::personData($xpath, $recipient),
            'recipient_is_taker' => self::samePerson($xpath, $recipient, $toma),
            'intermed' => self::personData($xpath, $intermed),
            'service' => [
                'tax_code' => self::serviceTaxCode($nationalTax, self::text($xpath, $serv, 'cTribMun')),
                'national_tax_code' => trim(self::formatNationalTaxCode($nationalTax) . ' - ' . self::descriptionWithoutCodePrefix($description), ' -') ?: '-',
                'municipal_tax_code' => self::text($xpath, $serv, 'cTribMun') ?: '-',
                'nbs_code' => self::formatNbsCode(self::text($xpath, $serv, 'cNBS')),
                'place_of_provision' => $serviceLocationCode ?: '-',
                'country' => $serviceCountry ?: '-',
                'place' => self::servicePlace($serviceCity, $serviceUf, $serviceCountry),
                'tax_code_description' => self::text($xpath, $infNfse, 'xTribMun') ?: self::text($xpath, $infNfse, 'xTribNac') ?: '-',
                'description' => $description ?: '-',
            ],
            'municipal_taxes' => [
                'issqn_tax' => self::issqnTax($issqnTaxType),
                'subject_to_issqn' => $issqnTaxType !== '4',
                'country' => self::text($xpath, $dps, 'cPaisResult') ?: '-',
                'city' => self::municipalIncidence($xpath, $infNfse, $dps, $incidenceUf),
                'special_tax_regim' => self::specialTaxRegime($regEspTrib),
                'immunity_type' => self::immunityType(self::text($xpath, $dps, 'tpImunidade')),
                'suspension_issqn' => '-',
                'suspension_number' => '-',
                'municipal_benefit' => self::municipalBenefit(self::text($xpath, $valores, 'tpBM')),
                'service_amount' => self::optionalCurrency(self::text($xpath, $dps, 'vServ')),
                'discount_unconditioned' => self::optionalCurrency(self::text($xpath, $dps, 'vDescIncond')),
                'deduct_reduc_amount' => self::deductionsReductions($xpath, $dps, $valores, $ibsCbs),
                'municipal_benefit_math' => self::optionalCurrency(
                    self::text($xpath, $valores, 'vCalcBM') ?: self::text($xpath, $dps, 'vRedBCBM'),
                ),
                'calculation_basis' => self::optionalCurrency(self::text($xpath, $valores, 'vBC')),
                'aliq_applied' => self::percentage(self::text($xpath, $valores, 'pAliqAplic')),
                'issqn_retention' => self::issqnRetention($issqnRetentionType),
                'issqn_cleared' => self::optionalCurrency($issqnValue),
            ],
            'federal_taxes' => [
                'printed' => $federalTaxBlockPrinted,
                'irrf' => self::optionalCurrency(self::text($xpath, $tribFed, 'vRetIRRF')),
                'previdenciary_contribution' => self::optionalCurrency($vRetCp),
                'social_contribution' => $usesLegacyPisCofinsRetention
                    ? self::sumCurrencyValues($vRetCsll, $pis, $cofins)
                    : self::optionalCurrency($vRetCsll),
                'social_description' => self::pisCofinsRetention($pisCofinsRetentionType),
                'pis_debit' => $usesLegacyPisCofinsRetention ? self::currency('0') : self::optionalCurrency($pis),
                'cofins_debit' => $usesLegacyPisCofinsRetention ? self::currency('0') : self::optionalCurrency($cofins),
            ],
            'ibs_cbs' => [
                'cst_classification' => self::slashJoin(self::text($xpath, $declaredIbsCbs, 'CST'), self::text($xpath, $declaredIbsCbs, 'cClassTrib')),
                'operation_indicator' => self::slashJoin(
                    self::text($xpath, $declaredIbsCbs, 'cIndOp'),
                    self::text($xpath, $calculatedIbsCbs, 'cLocalidadeIncid'),
                    self::text($xpath, $calculatedIbsCbs, 'xLocalidadeIncid'),
                    self::text($xpath, $calculatedIbsCbs, 'UF'),
                ),
                'exclusions_reductions' => self::currencyOrDash(self::sumRawValues(
                    self::text($xpath, $dps, 'vDescIncond'),
                    self::text($xpath, $ibsCbs, 'vCalcReeRepRes'),
                    self::text($xpath, $valores, 'vISSQN'),
                    $pis,
                    $cofins,
                )),
                'base_after_reductions' => self::optionalCurrency(self::text($xpath, $ibsCbs, 'vBC')),
                'rate_reductions' => self::slashJoin(
                    self::percentage(self::text($xpath, $ibsCbs, 'pRedAliqUF')),
                    self::percentage(self::text($xpath, $ibsCbs, 'pRedAliqMun')),
                    self::percentage(self::text($xpath, $ibsCbs, 'pRedAliqCBS')),
                ),
                'ibs_rates' => self::slashJoin(
                    self::percentage(self::text($xpath, $ibsCbs, 'pIBSUF')),
                    self::percentage(self::text($xpath, $ibsCbs, 'pIBSMun')),
                ),
                'ibs_municipal_effective_rate' => self::percentage(self::text($xpath, $ibsCbs, 'pAliqEfetMun')),
                'ibs_municipal_amount' => self::optionalCurrency(self::text($xpath, $ibsCbs, 'vIBSMun')),
                'ibs_state_effective_rate' => self::percentage(self::text($xpath, $ibsCbs, 'pAliqEfetUF')),
                'ibs_state_amount' => self::optionalCurrency(self::text($xpath, $ibsCbs, 'vIBSUF')),
                'ibs_total' => self::optionalCurrency(self::text($xpath, $ibsCbs, 'vIBSTot')),
                'cbs_rate' => self::percentage(self::text($xpath, $ibsCbs, 'pCBS')),
                'cbs_effective_rate' => self::percentage(self::text($xpath, $ibsCbs, 'pAliqEfetCBS')),
                'cbs_total' => self::optionalCurrency(self::text($xpath, $ibsCbs, 'vCBS')),
            ],
            'total_value' => [
                'service_amount' => self::optionalCurrency(self::text($xpath, $dps, 'vServ')),
                'discount_conditioned' => self::optionalCurrency(self::text($xpath, $dps, 'vDescCond')),
                'discount_unconditioned' => self::optionalCurrency(self::text($xpath, $dps, 'vDescIncond')),
                'total_retentions' => self::optionalCurrency(self::text($xpath, $valores, 'vTotalRet')),
                'issqn_retained' => self::currency((string) $issqnRetained),
                'total_federal_retentions' => self::currency((string) $totalFederalRetentions),
                'pis_cofins_debit' => $pisCofinsDebit > 0 ? self::currency((string) $pisCofinsDebit) : '-',
                'net_value' => self::optionalCurrency(self::text($xpath, $valores, 'vLiq')),
                'total_ibs_cbs' => $totalIbsCbs,
                'net_value_with_ibs_cbs' => self::optionalCurrency(self::text($xpath, $ibsCbs, 'vTotNF')),
            ],
            'taxes_amount' => [
                'federal_tax' => self::taxAmount($xpath, $dps, 'vTotTribFed', 'pTotTribFed'),
                'state_tax' => self::taxAmount($xpath, $dps, 'vTotTribEst', 'pTotTribEst'),
                'municipal_tax' => self::taxAmount($xpath, $dps, 'vTotTribMun', 'pTotTribMun'),
            ],
            'canhoto_nfse_key' => trim((self::text($xpath, $infNfse, 'nNFSe') ?: '-') . ' / ' . $key),
        ];

        $data['complementary_info'] = self::complementaryInfo($xpath, $dps, $serv, $data['taxes_amount']);

        self::applyOptionalMunicipalGroups($data, $xpath, $dps);

        return $data;
    }

    private static function firstElement(DOMXPath $xpath, ?DOMNode $context, string $tag): ?DOMElement
    {
        if (!$context) {
            return null;
        }

        $nodes = $xpath->query('.//*[local-name() = "' . $tag . '"]', $context);
        $node = $nodes?->item(0);

        return $node instanceof DOMElement ? $node : null;
    }

    private static function childElement(DOMXPath $xpath, ?DOMNode $context, string $tag): ?DOMElement
    {
        if (!$context) {
            return null;
        }

        $nodes = $xpath->query('./*[local-name() = "' . $tag . '"]', $context);
        $node = $nodes?->item(0);

        return $node instanceof DOMElement ? $node : null;
    }

    private static function text(DOMXPath $xpath, ?DOMNode $context, string $tag): string
    {
        $element = self::firstElement($xpath, $context, $tag);

        return trim((string) $element?->textContent);
    }

    private static function nfseKey(DOMElement $infNfse): string
    {
        $id = $infNfse->getAttribute('Id');

        return str_starts_with($id, 'NFS') ? substr($id, 3) : $id;
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function formatDateTime(string $value): array
    {
        if ($value === '') {
            return ['', ''];
        }

        $date = substr($value, 0, 10);
        $parts = explode('-', $date);

        if (count($parts) !== 3) {
            return [$date, substr($value, 11, 8)];
        }

        return [$parts[2] . '/' . $parts[1] . '/' . $parts[0], substr($value, 11, 8)];
    }

    private static function formatAddress(DOMXPath $xpath, ?DOMNode $node): string
    {
        if (!$node) {
            return '-';
        }

        $parts = array_filter([
            self::text($xpath, $node, 'xLgr'),
            self::text($xpath, $node, 'nro'),
            self::text($xpath, $node, 'xCpl'),
            self::text($xpath, $node, 'xBairro'),
        ], static fn (string $part): bool => $part !== '');

        return $parts === [] ? '-' : implode(', ', $parts);
    }

    /**
     * @return array<string, string>
     */
    private static function personData(DOMXPath $xpath, ?DOMNode $node): array
    {
        if (!$node) {
            return [
                'id' => '-',
                'municipal_registration' => '-',
                'phone' => '-',
                'name' => '-',
                'email' => '-',
                'address' => '-',
                'city' => '-',
                'city_state' => '-',
                'city_code_cep' => '-',
                'cep' => '-',
            ];
        }

        $municipalityCode = self::text($xpath, $node, 'cMun');
        $city = self::text($xpath, $node, 'xMun')
            ?: self::text($xpath, $node, 'xCidade')
            ?: self::municipalityName($municipalityCode)
            ?: $municipalityCode;
        $state = self::text($xpath, $node, 'UF')
            ?: self::text($xpath, $node, 'xEstProvReg')
            ?: self::ufFromIbgeCode($municipalityCode);
        $cep = self::text($xpath, $node, 'CEP');
        $postalCode = $cep !== '' ? self::formatCep($cep) : self::text($xpath, $node, 'cEndPost');

        return [
            'id' => self::taxIdentifier($xpath, $node) ?: '-',
            'municipal_registration' => self::text($xpath, $node, 'IM') ?: '-',
            'phone' => self::formatPhone(self::text($xpath, $node, 'fone')) ?: '-',
            'name' => self::text($xpath, $node, 'xNome') ?: '-',
            'email' => self::text($xpath, $node, 'email') ?: '-',
            'address' => self::formatAddress($xpath, $node),
            'city' => self::cityState($city, $state),
            'city_state' => self::cityState($city, $state),
            'city_code_cep' => self::slashJoin(self::text($xpath, $node, 'cMun'), $postalCode),
            'cep' => $postalCode ?: '-',
        ];
    }

    private static function taxIdentifier(DOMXPath $xpath, ?DOMNode ...$nodes): string
    {
        foreach ($nodes as $node) {
            $cnpj = self::text($xpath, $node, 'CNPJ');
            if ($cnpj !== '') {
                return self::formatCpfCnpj($cnpj);
            }

            $cpf = self::text($xpath, $node, 'CPF');
            if ($cpf !== '') {
                return self::formatCpfCnpj($cpf);
            }

            $nif = self::text($xpath, $node, 'NIF');
            if ($nif !== '') {
                return $nif;
            }
        }

        return '';
    }

    private static function samePerson(DOMXPath $xpath, ?DOMNode $first, ?DOMNode $second): bool
    {
        $firstIdentifier = self::taxIdentifier($xpath, $first);

        return $firstIdentifier !== '' && $firstIdentifier === self::taxIdentifier($xpath, $second);
    }

    private static function cityState(string $city, string $uf): string
    {
        return self::slashJoin($city, $uf);
    }

    private static function codeCep(string $cityCode, string $cep): string
    {
        return self::slashJoin($cityCode, self::formatCep($cep));
    }

    private static function serviceTaxCode(string $nationalTax, string $municipalTax): string
    {
        return self::slashJoin(self::formatNationalTaxCode($nationalTax), $municipalTax);
    }

    private static function servicePlace(string $city, string $uf, string $country): string
    {
        return self::slashJoin($city, $uf, $country);
    }

    private static function municipalIncidence(DOMXPath $xpath, DOMElement $infNfse, DOMElement $dps, string $incidenceUf): string
    {
        $incidenceCode = self::text($xpath, $infNfse, 'cLocIncid');

        return self::slashJoin(
            self::text($xpath, $infNfse, 'xLocIncid') ?: self::municipalityName($incidenceCode) ?: $incidenceCode,
            $incidenceUf,
            self::text($xpath, $dps, 'cPaisResult') ?: ($incidenceCode !== '' ? 'BR' : ''),
        );
    }

    private static function environmentNotice(string $environment): string
    {
        return $environment === '2' ? 'NFS-e SEM VALIDADE JURÍDICA' : '';
    }

    private static function environmentType(string $environment): string
    {
        return match ($environment) {
            '1' => 'Produção',
            '2' => 'Homologação',
            default => '-',
        };
    }

    private static function environmentGenerator(string $value): string
    {
        return match ($value) {
            '1' => 'Prefeitura',
            '2' => 'Sistema Nacional da NFS-e',
            default => $value === '' ? '-' : $value,
        };
    }

    private static function issuerType(string $value): string
    {
        return match ($value) {
            '1' => 'Prestador',
            '2' => 'Tomador',
            '3' => 'Intermediário',
            default => '-',
        };
    }

    private static function nfseStatus(string $value, bool $cancelled, bool $substituted): string
    {
        if ($cancelled) {
            return 'Cancelada';
        }

        if ($substituted) {
            return 'Substituída';
        }

        return match ($value) {
            '100' => 'NFS-e Gerada',
            '101' => 'NFS-e de Substituição Gerada',
            '102' => 'NFS-e de Decisão Judicial',
            '103' => 'NFS-e Avulsa',
            '107' => 'NFS-e MEI',
            default => $value === '' ? '-' : $value,
        };
    }

    private static function purpose(string $value): string
    {
        return match ($value) {
            '0' => 'NFS-e regular',
            '1' => 'NFS-e de crédito',
            '2' => 'NFS-e de débito',
            default => $value === '' ? '-' : $value,
        };
    }

    private static function formatCpfCnpj(string $value): string
    {
        $digits = preg_replace('/\D/', '', $value) ?? '';

        if ($digits === '') {
            return '';
        }

        if (strlen($digits) > 11) {
            $digits = str_pad(substr($digits, -14), 14, '0', STR_PAD_LEFT);

            return substr($digits, 0, 2) . '.' . substr($digits, 2, 3) . '.' . substr($digits, 5, 3) . '/' . substr($digits, 8, 4) . '-' . substr($digits, 12, 2);
        }

        $digits = str_pad(substr($digits, -11), 11, '0', STR_PAD_LEFT);

        return substr($digits, 0, 3) . '.' . substr($digits, 3, 3) . '.' . substr($digits, 6, 3) . '-' . substr($digits, 9, 2);
    }

    private static function formatCep(string $value): string
    {
        $digits = preg_replace('/\D/', '', $value) ?? '';

        if ($digits === '') {
            return '-';
        }

        $digits = str_pad(substr($digits, 0, 8), 8, '0', STR_PAD_RIGHT);

        return substr($digits, 0, 5) . '-' . substr($digits, 5, 3);
    }

    private static function formatPhone(string $value): string
    {
        $digits = preg_replace('/\D/', '', $value) ?? '';

        return match (strlen($digits)) {
            11 => '(' . substr($digits, 0, 2) . ') ' . substr($digits, 2, 5) . '-' . substr($digits, 7),
            10 => '(' . substr($digits, 0, 2) . ') ' . substr($digits, 2, 4) . '-' . substr($digits, 6),
            default => $value,
        };
    }

    private static function descriptionWithoutCodePrefix(string $description): string
    {
        if (str_contains($description, ' - ')) {
            return trim((string) preg_replace('/^.*? - /', '', $description));
        }

        return trim($description);
    }

    private static function formatNationalTaxCode(string $value): string
    {
        if (strlen($value) >= 6) {
            return substr($value, 0, 2) . '.' . substr($value, 2, 2) . '.' . substr($value, 4);
        }

        return $value;
    }

    private static function formatNbsCode(string $value): string
    {
        $digits = preg_replace('/\D/', '', $value) ?? '';

        if (strlen($digits) !== 9) {
            return $value === '' ? '-' : $value;
        }

        return substr($digits, 0, 1) . '.' . substr($digits, 1, 4) . '.' . substr($digits, 5, 2) . '.' . substr($digits, 7, 2);
    }

    private static function ufFromIbgeCode(string $value): string
    {
        if (!preg_match('/^\d{7}$/', $value)) {
            return '';
        }

        return match (substr($value, 0, 2)) {
            '11' => 'RO', '12' => 'AC', '13' => 'AM', '14' => 'RR', '15' => 'PA', '16' => 'AP', '17' => 'TO',
            '21' => 'MA', '22' => 'PI', '23' => 'CE', '24' => 'RN', '25' => 'PB', '26' => 'PE', '27' => 'AL', '28' => 'SE', '29' => 'BA',
            '31' => 'MG', '32' => 'ES', '33' => 'RJ', '35' => 'SP',
            '41' => 'PR', '42' => 'SC', '43' => 'RS',
            '50' => 'MS', '51' => 'MT', '52' => 'GO', '53' => 'DF',
            default => '',
        };
    }

    private static function municipalityName(string $code): string
    {
        if (!preg_match('/^\d{7}$/', $code)) {
            return '';
        }

        if (self::$municipalities === null) {
            self::$municipalities = [];
            $handle = @fopen(base_path('database/seeders/csvs/municipio.csv'), 'r');

            if ($handle !== false) {
                while (($row = fgetcsv($handle, escape: '')) !== false) {
                    if (isset($row[1], $row[2]) && preg_match('/^\d{7}$/', $row[1])) {
                        self::$municipalities[$row[1]] = $row[2];
                    }
                }

                fclose($handle);
            }
        }

        return self::$municipalities[$code] ?? '';
    }

    private static function number(string $value): float
    {
        if ($value === '') {
            return 0.0;
        }

        return (float) str_replace(',', '.', $value);
    }

    private static function currency(string $value): string
    {
        return 'R$ ' . number_format(self::number($value), 2, ',', '.');
    }

    private static function currencyOrDash(?float $value): string
    {
        return $value === null ? '-' : self::currency((string) $value);
    }

    private static function optionalCurrency(string $value): string
    {
        return $value === '' ? '-' : self::currency($value);
    }

    private static function percentage(string $value): string
    {
        return $value === '' ? '-' : number_format(self::number($value), 2, ',', '.') . '%';
    }

    private static function slashJoin(string ...$values): string
    {
        $parts = array_values(array_filter(
            array_map(static fn (string $value): string => trim($value), $values),
            static fn (string $value): bool => $value !== '' && $value !== '-',
        ));

        return $parts === [] ? '-' : implode(' / ', $parts);
    }

    private static function sumRawValues(string ...$values): ?float
    {
        $present = array_filter($values, static fn (string $value): bool => trim($value) !== '');

        if ($present === []) {
            return null;
        }

        return array_reduce(
            $present,
            static fn (float $carry, string $value): float => $carry + self::number($value),
            0.0,
        );
    }

    private static function sumCurrencyValues(string ...$values): string
    {
        return self::currencyOrDash(self::sumRawValues(...$values));
    }

    private static function simplesNacional(string $value): string
    {
        return match ($value) {
            '' => '-',
            '1' => 'Não Optante',
            '2' => 'Optante - Microempreendedor Individual (MEI)',
            '3' => 'Optante - Microempresa ou Empresa de Pequeno Porte (ME/EPP)',
            default => $value,
        };
    }

    private static function taxRegime(string $value): string
    {
        return match ($value) {
            '1' => 'Regime de apuração dos tributos federais e municipal pelo Simples Nacional',
            '2' => 'Regime de apuração dos tributos federais pelo SN e o ISSQN pela NFS-e conforme respectiva legislação municipal do tributo',
            '3' => 'Regime de apuração dos tributos federais e municipal pela NFS-e conforme respectivas legislações federal e municipal de cada tributo',
            default => '-',
        };
    }

    private static function specialTaxRegime(string $value): string
    {
        return match ($value) {
            '' => '-',
            '0' => 'Nenhum',
            '1' => 'Ato Cooperado (Cooperativa)',
            '2' => 'Estimativa',
            '3' => 'Microempresa Municipal',
            '4' => 'Notário ou Registrador',
            '5' => 'Profissional Autônomo',
            '6' => 'Sociedade de Profissionais',
            '9' => 'Outros',
            default => $value,
        };
    }

    private static function issqnTax(string $value): string
    {
        return match ($value) {
            '' => '-',
            '1' => 'Operação Tributável',
            '2' => 'Imunidade',
            '3' => 'Exportação de serviço',
            '4' => 'Não Incidência',
            default => $value,
        };
    }

    private static function municipalBenefit(string $value): string
    {
        return match ($value) {
            '' => '-',
            '1' => 'Isenção',
            '2' => 'Redução da BC em %',
            '3' => 'Redução da BC em R$',
            '4' => 'Alíquota Diferenciada',
            default => $value,
        };
    }

    private static function immunityType(string $value): string
    {
        $description = match ($value) {
            '' => '-',
            '0' => 'Imunidade (tipo não informado na nota de origem)',
            '1' => 'Patrimônio, renda ou serviços, uns dos outros',
            '2' => 'Templos de qualquer culto',
            '3' => 'Patrimônio, renda ou serviços dos partidos políticos, inclusive suas fundações, das entidades sindicais dos trabalhadores, das instituições de educação e de assistência social, sem fins lucrativos',
            '4' => 'Livros, jornais, periódicos e o papel destinado a sua impressão',
            '5' => 'Fonogramas e videofonogramas musicais produzidos no Brasil contendo obras musicais ou literomusicais de autores brasileiros',
            default => $value,
        };

        return self::truncate($description, 40);
    }

    private static function issqnRetention(string $value): string
    {
        return match ($value) {
            '' => '-',
            '1' => 'Não Retido',
            '2' => 'Retido pelo Tomador',
            '3' => 'Retido pelo Intermediário',
            default => $value,
        };
    }

    private static function pisCofinsRetention(string $value): string
    {
        return match ($value) {
            '' => '-',
            '0' => 'PIS/COFINS/CSLL Não Retidos',
            '1' => 'PIS/COFINS Retido',
            '2' => 'PIS/COFINS Não Retido',
            '3' => 'PIS/COFINS/CSLL Retidos',
            '4' => 'PIS/COFINS Retidos, CSLL Não Retido',
            '5' => 'PIS Retido, COFINS/CSLL Não Retidos',
            '6' => 'COFINS Retido, PIS/CSLL Não Retidos',
            '7' => 'PIS Não Retido, COFINS/CSLL Retidos',
            '8' => 'PIS/COFINS Não Retidos, CSLL Retido',
            '9' => 'COFINS Não Retido, PIS/CSLL Retidos',
            default => $value,
        };
    }

    private static function deductionsReductions(
        DOMXPath $xpath,
        DOMElement $dps,
        DOMElement $valores,
        ?DOMElement $ibsCbs,
    ): string {
        $declaredValue = self::text($xpath, $dps, 'vDR');
        if ($declaredValue !== '') {
            return self::currency($declaredValue);
        }

        return self::currencyOrDash(self::sumRawValues(
            self::text($xpath, $valores, 'vCalcDR'),
            self::text($xpath, $ibsCbs, 'vCalcReeRepRes'),
        ));
    }

    private static function taxAmount(
        DOMXPath $xpath,
        DOMElement $dps,
        string $valueTag,
        string $percentageTag,
    ): string {
        $value = self::text($xpath, $dps, $valueTag);
        if ($value !== '') {
            return self::currency($value);
        }

        return self::percentage(self::text($xpath, $dps, $percentageTag));
    }

    private static function watermark(bool $cancelled, bool $substituted): ?string
    {
        if ($cancelled) {
            return 'CANCELADA';
        }

        if ($substituted) {
            return 'SUBSTITUÍDA';
        }

        return null;
    }

    /**
     * @param  array<string, string>  $taxesAmount
     */
    private static function complementaryInfo(DOMXPath $xpath, DOMElement $dps, DOMElement $serv, array $taxesAmount): string
    {
        $infoCompl = self::firstElement($xpath, $serv, 'infoCompl');
        $parts = [
            'Inf. Cont.: ' . (self::text($xpath, $infoCompl, 'xInfComp') ?: '-'),
        ];

        foreach ([
            'NFS-e Subst.:' => self::text($xpath, $dps, 'chSubstda'),
            'Doc. Ref.:' => self::text($xpath, $dps, 'docRef'),
            'Cod. Obra:' => self::text($xpath, $dps, 'cObra'),
            'Insc. Imob.:' => self::text($xpath, $dps, 'inscImobFisc'),
            'Cod. Evt.:' => self::text($xpath, $dps, 'idAtvEvt'),
            'Doc. Tec.:' => self::text($xpath, $dps, 'idDocTec'),
            'Núm. Ped.:' => self::text($xpath, $dps, 'xPed'),
            'Item Ped.:' => self::text($xpath, $dps, 'xItemPed'),
            'Inf. A. T. Mun.:' => self::text($xpath, $infoCompl, 'xOutInf'),
        ] as $label => $value) {
            if ($value !== '') {
                $parts[] = $label . ' ' . $value;
            }
        }

        $taxes = sprintf(
            'Totais Aproximados dos Tributos cfe. Lei nº 12.741/2012: Federais: %s ; Estaduais: %s ; Municipais: %s',
            $taxesAmount['federal_tax'] ?? '-',
            $taxesAmount['state_tax'] ?? '-',
            $taxesAmount['municipal_tax'] ?? '-',
        );

        $separator = ' | ';
        $information = implode($separator, $parts);
        $availableLength = 2000 - mb_strlen($separator . $taxes);

        return self::truncate($information, max(3, $availableLength)) . $separator . $taxes;
    }

    private static function truncate(string $value, int $length): string
    {
        if (mb_strlen($value) <= $length) {
            return $value;
        }

        return mb_substr($value, 0, max(0, $length - 3)) . '...';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function applyOptionalMunicipalGroups(array &$data, DOMXPath $xpath, DOMNode $dps): void
    {
        $exigSusp = self::firstElement($xpath, $dps, 'exigSusp');
        if ($exigSusp) {
            $type = self::text($xpath, $exigSusp, 'tpSusp');
            $data['municipal_taxes']['suspension_issqn'] = match ($type) {
                '1' => 'Exigibilidade do ISSQN Suspensa por Decisão Judicial',
                '2' => 'Exigibilidade do ISSQN Suspensa por Processo Administrativo',
                default => 'Sim',
            };
            $data['municipal_taxes']['suspension_number'] = self::text($xpath, $exigSusp, 'nProcesso') ?: '-';
        }
    }
}