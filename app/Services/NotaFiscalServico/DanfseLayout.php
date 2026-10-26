<?php

declare(strict_types=1);

namespace App\Services\NotaFiscalServico;

final class DanfseLayout
{
    /** @var array<string, int> */
    private const FIELD_MAX_LENGTHS = [
        'Chave De Acesso Da NFS-e' => 50,
        'Número Da NFS-e' => 13,
        'Competência Da NFS-e' => 10,
        'Data E Hora Da Emissão Da NFS-e' => 19,
        'Número Da DPS' => 15,
        'Série Da DPS' => 5,
        'Data E Hora Da Emissão Da DPS' => 19,
        'Emitente Da NFS-e' => 13,
        'Situação Da NFS-e' => 40,
        'Finalidade' => 40,
        'CNPJ / CPF / NIF' => 40,
        'Indicador Municipal (Inscrição)' => 15,
        'Telefone' => 20,
        'Nome / Nome Empresarial' => 80,
        'Município / Sigla UF' => 37,
        'Código IBGE / CEP' => 21,
        'Endereço' => 100,
        'E-mail' => 80,
        'Simples Nacional Na Data De Competência' => 40,
        'Regime De Apuração Tributária Pelo SN' => 80,
        'Código De Tributação Nacional / Municipal' => 14,
        'Local Da Prestação / Sigla UF / País' => 42,
        'Descrição Do Serviço' => 1300,
        'Tipo De Tributação Do ISSQN' => 21,
        'Município / Sigla UF / País Da Incidência Do ISSQN' => 42,
        'Regime Especial De Tributação Do ISSQN' => 27,
        'Tipo De Imunidade Do ISSQN' => 40,
        'Suspensão Da Exigibilidade Do ISSQN' => 40,
        'Número Processo Suspensão' => 30,
        'Benefício Municipal' => 40,
        'Descrição Contrib. Sociais - Retidas' => 35,
        'CST / cClassTrib' => 12,
        'Indicador De Operação / Código IBGE Incidência / Município Incidência / Sigla UF' => 56,
        'Nº NFS-e / Chave NFS-e' => 66,
    ];

    /**
     * @param  array<string, mixed>  $data
     * @return array{cells: array<int, array<string, mixed>>, messages: array<int, array<string, mixed>>, rules: array<int, float>}
     */
    public static function build(array $data): array
    {
        /** @var array<string, string> $issuer */
        $issuer = $data['issuer'];
        /** @var array<string, string> $taker */
        $taker = $data['taker'];
        /** @var array<string, string> $recipient */
        $recipient = $data['recipient'];
        /** @var array<string, string> $intermed */
        $intermed = $data['intermed'];
        /** @var array<string, string> $service */
        $service = $data['service'];
        /** @var array<string, mixed> $municipal */
        $municipal = $data['municipal_taxes'];
        /** @var array<string, mixed> $federal */
        $federal = $data['federal_taxes'];
        /** @var array<string, string> $ibsCbs */
        $ibsCbs = $data['ibs_cbs'];
        /** @var array<string, string> $totals */
        $totals = $data['total_value'];

        $cells = [
            self::documentCell('Chave De Acesso Da NFS-e', (string) $data['key_nfse'], 0.30, 1.48, 15.30, 0.77, 'id-field'),
            self::documentCell('Número Da NFS-e', (string) $data['nfse_number'], 0.30, 2.27, 5.09, 0.67),
            self::documentCell('Competência Da NFS-e', (string) $data['compet'], 5.41, 2.27, 5.09, 0.67),
            self::documentCell('Data E Hora Da Emissão Da NFS-e', trim((string) $data['dt_nfse'] . ' ' . (string) $data['hr_nfse']), 10.51, 2.27, 5.09, 0.67),
            self::documentCell('Número Da DPS', (string) $data['dps_number'], 0.30, 2.96, 5.09, 0.67),
            self::documentCell('Série Da DPS', (string) $data['dps_serie'], 5.41, 2.96, 5.09, 0.67),
            self::documentCell('Data E Hora Da Emissão Da DPS', trim((string) $data['dt_dps'] . ' ' . (string) $data['hr_dps']), 10.51, 2.96, 5.09, 0.67),
            self::documentCell('Emitente Da NFS-e', (string) $data['issuer_type'], 0.30, 3.65, 5.09, 0.67, 'highlight'),
            self::documentCell('Situação Da NFS-e', (string) $data['nfse_status'], 5.41, 3.65, 5.09, 0.67),
            self::documentCell('Finalidade', (string) $data['purpose'], 10.51, 3.65, 5.09, 0.67),

            self::sectionCell('Prestador / Fornecedor', 0.30, 4.34),
            self::cell('CNPJ / CPF / NIF', $issuer['id'], 5.41, 4.34, 5.09, 0.63),
            self::cell('Indicador Municipal (Inscrição)', $issuer['municipal_registration'], 10.51, 4.34, 5.09, 0.63),
            self::cell('Telefone', $issuer['phone'], 15.62, 4.34, 5.09, 0.63),
            self::cell('Nome / Nome Empresarial', $issuer['name'], 0.30, 4.98, 10.19, 0.63),
            self::cell('Município / Sigla UF', $issuer['city_state'], 10.51, 4.98, 5.09, 0.63),
            self::cell('Código IBGE / CEP', $issuer['city_code_cep'], 15.62, 4.98, 5.09, 0.63),
            self::cell('Endereço', $issuer['address'], 0.30, 5.62, 10.19, 0.63),
            self::cell('E-mail', $issuer['email'], 10.51, 5.62, 10.19, 0.63),
            self::cell('Simples Nacional Na Data De Competência', $issuer['simples'], 0.30, 6.28, 5.09, 0.63),
            self::cell('Regime De Apuração Tributária Pelo SN', $issuer['tax_regim'], 10.51, 6.28, 10.19, 0.63),
        ];

        $messages = [];
        $rules = [1.48, 20.34, 6.92];
        $y = 6.92;

        $y = self::appendPersonBlock(
            $cells,
            $messages,
            'Tomador / Adquirente',
            $taker,
            $y,
            true,
            'TOMADOR/ADQUIRENTE DA OPERAÇÃO NÃO IDENTIFICADO NA NFS-e',
        );

        $rules[] = $y + 0.2; //8.86
        
        $y = 9.0;
        $y = self::appendRecipientBlock($cells, $messages, $recipient, $y, (bool) ($data['recipient_is_taker'] ?? false));
        
        $rules[] = $y;
        $y = self::appendPersonBlock(
            $cells,
            $messages,
            'Intermediário Da Operação',
            $intermed,
            $y,
            true,
            'INTERMEDIÁRIO DA OPERAÇÃO NÃO IDENTIFICADO NA NFS-e',
        );

        $rules[] = $y;
        $cells = [
            ...$cells,
            self::sectionCell('Serviço Prestado', 0.30, $y),
            self::cell('Código De Tributação Nacional / Municipal', $service['tax_code'], 5.41, $y, 5.09, 0.63),
            self::cell('Código Da NBS', $service['nbs_code'], 10.51, $y, 5.09, 0.63),
            self::cell('Local Da Prestação / Sigla UF / País', $service['place'], 15.62, $y, 5.09, 0.63),
            self::cell('', $service['tax_code_description'], 0.30, $y + 0.65, 20.40, 0.38, 'no-label'),
            self::cell('Descrição Do Serviço', $service['description'], 0.30, $y + 1.05, 20.40, 10.63),
        ];
        $y += 2.69;

        $rules[] = $y;
        if ($municipal['subject_to_issqn'] ?? true) {
            $cells = [
                ...$cells,
                self::sectionCell('Tributação Municipal (ISSQN)', 0.30, $y),
                self::cell('Tipo De Tributação Do ISSQN', $municipal['issqn_tax'], 5.41, $y, 5.09, 0.63),
                self::cell('Município / Sigla UF / País Da Incidência Do ISSQN', $municipal['city'], 10.51, $y, 10.19, 0.63),
                self::cell('Regime Especial De Tributação Do ISSQN', $municipal['special_tax_regim'], 0.30, $y + 0.65, 5.09, 0.63),
                self::cell('Tipo De Imunidade Do ISSQN', $municipal['immunity_type'], 5.41, $y + 0.65, 5.09, 0.63),
                self::cell('Suspensão Da Exigibilidade Do ISSQN', $municipal['suspension_issqn'], 10.51, $y + 0.65, 5.09, 0.63),
                self::cell('Número Processo Suspensão', $municipal['suspension_number'], 15.62, $y + 0.65, 5.09, 0.63),
                self::cell('Benefício Municipal', $municipal['municipal_benefit'], 0.30, $y + 1.30, 5.09, 0.63),
                self::cell('Cálculo Do BM', $municipal['municipal_benefit_math'], 5.41, $y + 1.30, 5.09, 0.63),
                self::cell('Total Deduções/Reduções', $municipal['deduct_reduc_amount'], 10.51, $y + 1.30, 5.09, 0.63),
                self::cell('Desconto Incondicionado', $municipal['discount_unconditioned'], 15.62, $y + 1.30, 5.09, 0.63),
                self::cell('BC ISSQN', $municipal['calculation_basis'], 0.30, $y + 1.94, 5.09, 0.63),
                self::cell('Alíquota Aplicada', $municipal['aliq_applied'], 5.41, $y + 1.94, 5.09, 0.63),
                self::cell('Retenção Do ISSQN', $municipal['issqn_retention'], 10.51, $y + 1.94, 5.09, 0.63),
                self::cell('ISSQN Apurado', $municipal['issqn_cleared'], 15.62, $y + 1.94, 5.09, 0.63),
            ];
            $y += 2.59;
        } else {
            $messages[] = self::message('TRIBUTAÇÃO MUNICIPAL (ISSQN) - OPERAÇÃO NÃO SUJEITA AO ISSQN', $y);
            $y += 0.32;
        }

        if ($federal['printed'] ?? true) {
            $rules[] = $y;
            $cells = [
                ...$cells,
                self::sectionCell('Tributação Federal (Exceto CBS)', 0.30, $y),
                self::cell('IRRF', (string) $federal['irrf'], 5.41, $y, 5.09, 0.63),
                self::cell('Contribuição Previdenciária - Retida', (string) $federal['previdenciary_contribution'], 10.51, $y, 5.09, 0.63),
                self::cell('Contribuições Sociais - Retidas', (string) $federal['social_contribution'], 15.62, $y, 5.09, 0.63),
                self::cell('PIS - Débito Apuração Própria', (string) $federal['pis_debit'], 0.30, $y + 0.65, 5.09, 0.63),
                self::cell('COFINS - Débito Apuração Própria', (string) $federal['cofins_debit'], 5.41, $y + 0.65, 5.09, 0.63),
                self::cell('Descrição Contrib. Sociais - Retidas', (string) $federal['social_description'], 10.51, $y + 0.65, 10.19, 0.63),
            ];
            $y += 1.30;
        }

        $rules[] = $y;
        $cells = [
            ...$cells,
            self::sectionCell('Tributação IBS / CBS', 0.30, $y),
            self::cell('CST / cClassTrib', $ibsCbs['cst_classification'], 5.41, $y, 5.09, 0.63),
            self::cell('Indicador De Operação / Código IBGE Incidência / Município Incidência / Sigla UF', $ibsCbs['operation_indicator'], 10.51, $y, 10.19, 0.63),
            self::cell('Exclusões E Reduções Da Base De Cálculo', $ibsCbs['exclusions_reductions'], 0.30, $y + 0.64, 5.09, 0.63),
            self::cell('Base De Cálculo Após Exclusões E Reduções', $ibsCbs['base_after_reductions'], 5.41, $y + 0.64, 5.09, 0.63),
            self::cell('Red. Alíquota IBS / Red. Alíquota CBS', $ibsCbs['rate_reductions'], 10.51, $y + 0.64, 5.09, 0.63),
            self::cell('Alíquota - IBS UF / IBS Mun', $ibsCbs['ibs_rates'], 15.62, $y + 0.64, 5.09, 0.63),
            self::cell('Alíq. Efetiva Municipal - IBS', $ibsCbs['ibs_municipal_effective_rate'], 0.30, $y + 1.29, 5.09, 0.63),
            self::cell('Valor Apurado Municipal - IBS', $ibsCbs['ibs_municipal_amount'], 5.41, $y + 1.29, 5.09, 0.63),
            self::cell('Alíq. Efetiva Estadual - IBS', $ibsCbs['ibs_state_effective_rate'], 10.51, $y + 1.29, 5.09, 0.63),
            self::cell('Valor Apurado Estadual - IBS', $ibsCbs['ibs_state_amount'], 15.62, $y + 1.29, 5.09, 0.63),
            self::cell('Valor Total Apurado - IBS', $ibsCbs['ibs_total'], 0.30, $y + 1.94, 5.09, 0.63),
            self::cell('Alíquota - CBS', $ibsCbs['cbs_rate'], 5.41, $y + 1.94, 5.09, 0.63),
            self::cell('Alíquota Efetiva - CBS', $ibsCbs['cbs_effective_rate'], 10.51, $y + 1.94, 5.09, 0.63),
            self::cell('Valor Total Apurado - CBS', $ibsCbs['cbs_total'], 15.62, $y + 1.94, 5.09, 0.63),
        ];
        $y += 2.58;

        $rules[] = $y;
        $cells = [
            ...$cells,
            self::sectionCell('Valor Total Da NFS-e', 0.30, $y, 5.09, 0.67),
            self::cell('Valor Da Operação / Serviço', $totals['service_amount'], 5.41, $y, 5.09, 0.67),
            self::cell('Desconto Incondicionado', $totals['discount_unconditioned'], 10.51, $y, 5.09, 0.67),
            self::cell('Desconto Condicionado', $totals['discount_conditioned'], 15.62, $y, 5.09, 0.67),
            self::cell('Total Das Retenções (ISSQN / Federais)', $totals['total_retentions'], 0.30, $y + 0.69, 5.09, 0.67),
            self::cell('Valor Líquido Da NFS-e', $totals['net_value'], 5.41, $y + 0.69, 5.09, 0.67),
            self::cell('Total Do IBS/CBS', $totals['total_ibs_cbs'], 10.51, $y + 0.69, 5.09, 0.67),
            self::cell('Valor Líquido Da NFS-e + IBS/CBS', $totals['net_value_with_ibs_cbs'], 15.62, $y + 0.69, 5.09, 0.67, 'highlight'),
        ];
        $y += 1.37;

        $rules[] = $y + 10;
        $complementaryTop = $y + 0.41;
        $cells = [
            ...$cells,
            self::sectionCell('Informações Complementares', 0.30, $y, 20.40, 0.39),
            self::cell('', (string) $data['complementary_info'], 0.30, $complementaryTop, 20.40, max(0.39, 28.08 - $complementaryTop), 'large-text no-label'),
            self::blankCell('Data Cientificação', 0.30, 28.10, 4.94, 0.67),
            self::blankCell('Identificação E Assinatura', 5.41, 28.10, 4.94, 0.67),
            self::cell('Nº NFS-e / Chave NFS-e', (string) $data['canhoto_nfse_key'], 10.51, 28.10, 10.19, 0.67, 'boxed'),
        ];

        return [
            'cells' => $cells,
            'messages' => $messages,
            'rules' => array_values(array_unique($rules)),
        ];
    }

    /**
     * @param  array<string, string>  $person
     * @return array<int, array<string, mixed>>
     */
    private static function personBlock(string $title, array $person, float $top, bool $hasMunicipalRegistration): array
    {
        if (self::isEmptyPerson($person)) {
            return [];
        }

        $cells = [
            self::sectionCell($title, 0.30, $top),
            self::cell('CNPJ / CPF / NIF', $person['id'], 5.41, $top, 5.09, 0.63),
        ];

        if ($hasMunicipalRegistration) {
            $cells[] = self::cell('Indicador Municipal (Inscrição)', $person['municipal_registration'], 10.51, $top, 5.09, 0.63);
        }

        $cells = [
            ...$cells,
            self::cell('Telefone', $person['phone'], 15.62, $top, 5.09, 0.63),
            self::cell('Nome / Nome Empresarial', $person['name'], 0.30, $top + 0.64, 10.19, 0.63),
            self::cell('Município / Sigla UF', $person['city_state'], 10.51, $top + 0.64, 5.09, 0.63),
            self::cell('Código IBGE / CEP', $person['city_code_cep'], 15.62, $top + 0.64, 5.09, 0.63),
            self::cell('Endereço', $person['address'], 0.30, $top + 1.30, 10.19, 10.63),
            self::cell('E-mail', $person['email'], 10.51, $top + 1.30, 10.19, 10.63),
        ];

        return $cells;
    }

    /**
     * @param  array<int, array<string, mixed>>  $cells
     * @param  array<int, array<string, mixed>>  $messages
     * @param  array<string, string>  $person
     */
    private static function appendPersonBlock(
        array &$cells,
        array &$messages,
        string $title,
        array $person,
        float $top,
        bool $hasMunicipalRegistration,
        string $missingMessage,
    ): float {
        if (self::isEmptyPerson($person)) {
            $messages[] = self::message($missingMessage, $top);

            return $top + 0.32;
        }

        $cells = [...$cells, ...self::personBlock($title, $person, $top, $hasMunicipalRegistration)];

        return $top + 1.94;
    }

    /**
     * @param  array<int, array<string, mixed>>  $cells
     * @param  array<int, array<string, mixed>>  $messages
     * @param  array<string, string>  $recipient
     */
    private static function appendRecipientBlock(
        array &$cells,
        array &$messages,
        array $recipient,
        float $top,
        bool $recipientIsTaker,
    ): float {
        //$top+= 0.32;//top item destinatário josue
        if (self::isEmptyPerson($recipient)) {
            $messages[] = self::message('DESTINATÁRIO DA OPERAÇÃO NÃO IDENTIFICADO NA NFS-e', $top);

            return $top + 0.32;
        }

        if ($recipientIsTaker) {
            $messages[] = self::message('O DESTINATÁRIO É O PRÓPRIO TOMADOR/ADQUIRENTE DA OPERAÇÃO', $top);

            return $top + 0.32;
        }

        $cells = [...$cells, ...self::personBlock('Destinatário Da Operação', $recipient, $top, false)];

        return $top + 1.94;
    }

    /** @return array<string, mixed> */
    private static function message(string $text, float $top): array
    {
        return [
            'text' => $text,
            'x' => 0.30,
            'y' => round($top, 2),
            'w' => 20.40,
            'h' => 0.32,
        ];
    }

    /**
     * @param  array<string, string>  $person
     */
    private static function isEmptyPerson(array $person): bool
    {
        return ($person['id'] ?? '-') === '-'
            && ($person['name'] ?? '-') === '-'
            && ($person['phone'] ?? '-') === '-'
            && ($person['email'] ?? '-') === '-';
    }

    /**
     * @return array<string, mixed>
     */
    private static function sectionCell(string $label, float $x, float $y, float $w = 5.09, float $h = 0.63): array
    {
        return self::cell($label, '', $x, $y, $w, $h, 'section-cell');
    }

    /**
     * @return array<string, mixed>
     */
    private static function documentCell(string $label, string $value, float $x, float $y, float $w, float $h, string $class = ''): array
    {
        return self::cell($label, $value, $x, $y, $w, $h, trim('document-field ' . $class));
    }

    /** @return array<string, mixed> */
    private static function blankCell(string $label, float $x, float $y, float $w, float $h): array
    {
        $cell = self::cell($label, '-', $x, $y, $w, $h, 'boxed');
        $cell['value'] = '';

        return $cell;
    }

    /**
     * @return array<string, mixed>
     */
    private static function cell(string $label, string $value, float $x, float $y, float $w, float $h, string $class = ''): array
    {
        $value = $value === '' ? '-' : $value;
        $maximumLength = match (true) {
            str_contains($class, 'large-text') => 2000,
            str_contains($class, 'no-label') => 170,
            default => self::FIELD_MAX_LENGTHS[$label] ?? 0,
        };

        if ($maximumLength > 0 && mb_strlen($value) > $maximumLength) {
            $value = mb_substr($value, 0, $maximumLength - 3) . '...';
        }

        return [
            'label' => $label,
            'value' => $value,
            'x' => round($x, 2),
            'y' => round($y, 2),
            'w' => round($w, 2),
            'h' => round($h, 2),
            'class' => $class,
        ];
    }
}