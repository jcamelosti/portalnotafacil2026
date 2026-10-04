<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        DANFSe -
        {{ $data['header']['numero_nfse'] ?? $nota->id }}
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #e5e5e5;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }

        body {
            font-size: 11px;
        }

        /* =====================================================
           ÁREA DA PÁGINA
        ===================================================== */

        .pagina {
            width: 210mm;
            min-height: 297mm;
            margin: 15px auto;
            padding: 10mm;
            background: #fff;
            box-shadow: 0 0 8px rgba(0,0,0,.25);
        }

        /* =====================================================
           BOTÕES
        ===================================================== */

        .barra-acoes {
            width: 210mm;
            margin: 15px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .btn {
            border: 0;
            padding: 9px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }

        .btn-imprimir {
            background: #198754;
            color: #fff;
        }

        .btn-fechar {
            background: #6c757d;
            color: #fff;
        }

        /* =====================================================
           CABEÇALHO
        ===================================================== */

        .cabecalho {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
        }

        .cabecalho td {
            border: 1px solid #000;
            padding: 7px;
            vertical-align: middle;
        }

        .logo-nfse {
            width: 22%;
            text-align: center;
            font-weight: bold;
            font-size: 20px;
        }

        .logo-nfse small {
            display: block;
            font-size: 9px;
            font-weight: normal;
            margin-top: 3px;
        }

        .cabecalho-municipio {
            width: 43%;
        }

        .cabecalho-numero {
            width: 35%;
            text-align: center;
        }

        .numero {
            font-size: 18px;
            font-weight: bold;
        }

        /* =====================================================
           SEÇÕES
        ===================================================== */

        .secao {
            margin-top: 8px;
            background: #e9ecef;
            border: 1px solid #000;
            padding: 5px 7px;
            font-weight: bold;
            font-size: 11px;
        }

        /* =====================================================
           TABELAS
        ===================================================== */

        .tabela {
            width: 100%;
            border-collapse: collapse;
        }

        .tabela td,
        .tabela th {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
        }

        .tabela th {
            background: #f2f2f2;
            font-weight: bold;
        }

        /* =====================================================
           CAMPOS
        ===================================================== */

        .label {
            display: block;
            font-size: 8px;
            color: #555;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .valor {
            font-size: 10px;
        }

        .valor-negrito {
            font-size: 10px;
            font-weight: bold;
        }

        .valor-grande {
            font-size: 13px;
            font-weight: bold;
        }

        .texto {
            font-size: 10px;
            line-height: 1.35;
        }

        .centro {
            text-align: center;
        }

        .direita {
            text-align: right;
        }

        /* =====================================================
           TOTAIS
        ===================================================== */

        .total-principal {
            font-size: 14px;
            font-weight: bold;
        }

        .total-box {
            background: #f7f7f7;
        }

        /* =====================================================
           INFORMAÇÕES COMPLEMENTARES
        ===================================================== */

        .informacoes {
            font-size: 9px;
            line-height: 1.45;
        }

        /* =====================================================
           RODAPÉ
        ===================================================== */

        .rodape {
            margin-top: 10px;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 8px;
            color: #444;
        }

        /* =====================================================
           IMPRESSÃO
        ===================================================== */

        @media print {

            @page {
                size: A4 portrait;
                margin: 8mm;
            }

            html,
            body {
                background: #fff;
            }

            .barra-acoes {
                display: none !important;
            }

            .pagina {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .secao {
                break-after: avoid;
            }

            .tabela {
                break-inside: auto;
            }

            tr {
                break-inside: avoid;
            }

            .rodape {
                break-inside: avoid;
            }

        }

    </style>

</head>

<body>

{{-- =========================================================
     BOTÕES
========================================================= --}}

<div class="barra-acoes">

    <button
        type="button"
        class="btn btn-imprimir"
        onclick="window.print()"
    >
        🖨 Imprimir / Salvar PDF
    </button>

    <button
        type="button"
        class="btn btn-fechar"
        onclick="window.close()"
    >
        Fechar
    </button>

</div>


{{-- =========================================================
     DANFSe
========================================================= --}}

<div class="pagina">


    {{-- =====================================================
         CABEÇALHO
    ====================================================== --}}

    <table class="cabecalho">

        <tr>

            <td class="logo-nfse">

                NFS-e

                <small>
                    Nota Fiscal de Serviço eletrônica
                </small>

            </td>

            <td class="cabecalho-municipio">

                <span class="label">
                    Município
                </span>

                <span class="valor-grande">
                    {{ $data['header']['municipio'] ?? '-' }}
                </span>

            </td>

            <td class="cabecalho-numero">

                <span class="label">
                    Número da NFS-e
                </span>

                <span class="numero">
                    {{ $data['header']['numero_nfse'] ?? '-' }}
                </span>

            </td>

        </tr>

    </table>


    {{-- =====================================================
         IDENTIFICAÇÃO
    ====================================================== --}}

    <div class="secao">
        IDENTIFICAÇÃO DA NFS-e
    </div>

    <table class="tabela">

        <tr>

            <td colspan="3">

                <span class="label">
                    Chave de acesso da NFS-e
                </span>

                <span class="valor-negrito">
                    {{ $data['header']['chave'] ?? '-' }}
                </span>

            </td>

        </tr>

        <tr>

            <td width="40%">

                <span class="label">
                    Situação
                </span>

                <span class="valor">
                    {{ $data['header']['situacao'] ?? '-' }}
                </span>

            </td>

            <td width="30%">

                <span class="label">
                    Finalidade
                </span>

                <span class="valor">
                    {{ $data['header']['finalidade'] ?? '-' }}
                </span>

            </td>

            <td width="30%">

                <span class="label">
                    Competência
                </span>

                <span class="valor">
                    {{ $data['header']['competencia'] ?? '-' }}
                </span>

            </td>

        </tr>

        <tr>

            <td>

                <span class="label">
                    Emissão NFS-e
                </span>

                <span class="valor">
                    {{ $data['header']['emissao_nfse'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Número DPS
                </span>

                <span class="valor">
                    {{ $data['header']['numero_dps'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Série DPS
                </span>

                <span class="valor">
                    {{ $data['header']['serie_dps'] ?? '-' }}
                </span>

            </td>

        </tr>

        <tr>

            <td colspan="2">

                <span class="label">
                    Emissão DPS
                </span>

                <span class="valor">
                    {{ $data['header']['emissao_dps'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Ambiente
                </span>

                <span class="valor">

                    @if(($data['header']['tipo_ambiente'] ?? '') == '1')
                        Produção
                    @elseif(($data['header']['tipo_ambiente'] ?? '') == '2')
                        Homologação
                    @else
                        {{ $data['header']['tipo_ambiente'] ?? '-' }}
                    @endif

                </span>

            </td>

        </tr>

    </table>


    {{-- =====================================================
         PRESTADOR
    ====================================================== --}}

    <div class="secao">
        PRESTADOR DO SERVIÇO
    </div>

    <table class="tabela">

        <tr>

            <td width="20%">

                <span class="label">
                    CNPJ
                </span>

                <span class="valor-negrito">
                    {{ $data['prestador']['cnpj'] ?? '-' }}
                </span>

            </td>

            <td width="18%">

                <span class="label">
                    Inscrição Municipal
                </span>

                <span class="valor">
                    {{ $data['prestador']['im'] ?? '-' }}
                </span>

            </td>

            <td width="62%">

                <span class="label">
                    Razão Social
                </span>

                <span class="valor-negrito">
                    {{ $data['prestador']['nome'] ?? '-' }}
                </span>

            </td>

        </tr>

        <tr>

            <td colspan="2">

                <span class="label">
                    Nome Fantasia
                </span>

                <span class="valor">
                    {{ $data['prestador']['fantasia'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    E-mail
                </span>

                <span class="valor">
                    {{ $data['prestador']['email'] ?? '-' }}
                </span>

            </td>

        </tr>

        <tr>

            <td colspan="2">

                <span class="label">
                    Endereço
                </span>

                <span class="valor">
                    {{ $data['prestador']['endereco'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Bairro
                </span>

                <span class="valor">
                    {{ $data['prestador']['bairro'] ?? '-' }}
                </span>

            </td>

        </tr>

        <tr>

            <td>

                <span class="label">
                    Município
                </span>

                <span class="valor">
                    {{ $data['prestador']['municipio'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    UF
                </span>

                <span class="valor">
                    {{ $data['prestador']['uf'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    CEP / Telefone
                </span>

                <span class="valor">

                    {{ $data['prestador']['cep'] ?? '-' }}

                    @if(!empty($data['prestador']['fone']))
                        / {{ $data['prestador']['fone'] }}
                    @endif

                </span>

            </td>

        </tr>

        <tr>

            <td>

                <span class="label">
                    Opção pelo Simples Nacional
                </span>

                <span class="valor">
                    {{ $data['prestador']['op_simples'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Regime de Apuração
                </span>

                <span class="valor">
                    {{ $data['prestador']['reg_apuracao'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Regime Especial
                </span>

                <span class="valor">
                    {{ $data['prestador']['regime_especial'] ?? '-' }}
                </span>

            </td>

        </tr>

    </table>


    {{-- =====================================================
         TOMADOR
    ====================================================== --}}

    <div class="secao">
        TOMADOR / ADQUIRENTE DO SERVIÇO
    </div>

    <table class="tabela">

        <tr>

            <td width="22%">

                <span class="label">
                    CNPJ
                </span>

                <span class="valor-negrito">
                    {{ $data['tomador']['cnpj'] ?? '-' }}
                </span>

            </td>

            <td width="78%">

                <span class="label">
                    Razão Social
                </span>

                <span class="valor-negrito">
                    {{ $data['tomador']['nome'] ?? '-' }}
                </span>

            </td>

        </tr>

        <tr>

            <td colspan="2">

                <span class="label">
                    Endereço
                </span>

                <span class="valor">
                    {{ $data['tomador']['endereco'] ?? '-' }}
                </span>

            </td>

        </tr>

        <tr>

            <td>

                <span class="label">
                    Bairro
                </span>

                <span class="valor">
                    {{ $data['tomador']['bairro'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Município / UF / CEP
                </span>

                <span class="valor">

                    {{ $data['tomador']['municipio'] ?? '-' }}

                    /

                    {{ $data['tomador']['uf'] ?? '-' }}

                    /

                    {{ $data['tomador']['cep'] ?? '-' }}

                </span>

            </td>

        </tr>

        <tr>

            <td>

                <span class="label">
                    Telefone
                </span>

                <span class="valor">
                    {{ $data['tomador']['fone'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    E-mail
                </span>

                <span class="valor">
                    {{ $data['tomador']['email'] ?? '-' }}
                </span>

            </td>

        </tr>

    </table>


    {{-- =====================================================
         SERVIÇO
    ====================================================== --}}

    <div class="secao">
        SERVIÇO PRESTADO
    </div>

    <table class="tabela">

        <tr>

            <td width="22%">

                <span class="label">
                    Código Tributação Nacional
                </span>

                <span class="valor">
                    {{ $data['servico']['codigo_tributacao_nacional'] ?? '-' }}
                </span>

            </td>

            <td width="15%">

                <span class="label">
                    Código Municipal
                </span>

                <span class="valor">
                    {{ $data['servico']['codigo_tributacao_municipal'] ?? '-' }}
                </span>

            </td>

            <td width="18%">

                <span class="label">
                    NBS
                </span>

                <span class="valor">
                    {{ $data['servico']['nbs'] ?? '-' }}
                </span>

            </td>

            <td width="45%">

                <span class="label">
                    Local da Prestação
                </span>

                <span class="valor">

                    {{ $data['servico']['local_prestacao'] ?? '-' }}

                    /

                    {{ $data['servico']['uf_prestacao'] ?? '-' }}

                </span>

            </td>

        </tr>

        <tr>

            <td colspan="4">

                <span class="label">
                    Descrição do Serviço
                </span>

                <div class="texto">
                    {{ $data['servico']['descricao'] ?? '-' }}
                </div>

            </td>

        </tr>

        <tr>

            <td colspan="2">

                <span class="label">
                    Tributação Nacional
                </span>

                <div class="texto">
                    {{ $data['servico']['descricao_tributacao_nacional'] ?? '-' }}
                </div>

            </td>

            <td colspan="2">

                <span class="label">
                    Tributação Municipal
                </span>

                <div class="texto">
                    {{ $data['servico']['descricao_tributacao_municipal'] ?? '-' }}
                </div>

            </td>

        </tr>

        <tr>

            <td colspan="3">

                <span class="label">
                    Descrição NBS
                </span>

                <div class="texto">
                    {{ $data['servico']['descricao_nbs'] ?? '-' }}
                </div>

            </td>

            <td>

                <span class="label">
                    País
                </span>

                <span class="valor">
                    {{ $data['servico']['pais'] ?? '-' }}
                </span>

            </td>

        </tr>

    </table>


    {{-- =====================================================
         ISSQN
    ====================================================== --}}

    <div class="secao">
        TRIBUTAÇÃO MUNICIPAL — ISSQN
    </div>

    <table class="tabela">

        <tr>

            <td>

                <span class="label">
                    Regime Especial
                </span>

                <span class="valor">
                    {{ $data['municipal']['regime_especial'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Tipo de Tributação
                </span>

                <span class="valor">
                    {{ $data['municipal']['tipo_tributacao'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Imunidade
                </span>

                <span class="valor">
                    {{ $data['municipal']['tipo_imunidade'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Retenção
                </span>

                <span class="valor">
                    {{ $data['municipal']['retencao'] ?? '-' }}
                </span>

            </td>

        </tr>

        <tr>

            <td>

                <span class="label">
                    Base de Cálculo
                </span>

                <span class="valor-negrito">
                    {{ $data['municipal']['base_calculo'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Alíquota
                </span>

                <span class="valor-negrito">
                    {{ $data['municipal']['aliquota'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    ISSQN Apurado
                </span>

                <span class="valor-negrito">
                    {{ $data['municipal']['issqn_apurado'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Suspensão
                </span>

                <span class="valor">
                    {{ $data['municipal']['suspensao'] ?? '-' }}
                </span>

            </td>

        </tr>

        <tr>

            <td colspan="4">

                <span class="label">
                    Número do Processo de Suspensão
                </span>

                <span class="valor">
                    {{ $data['municipal']['numero_processo_suspensao'] ?? '-' }}
                </span>

            </td>

        </tr>

    </table>


    {{-- =====================================================
         TRIBUTOS FEDERAIS
    ====================================================== --}}

    <div class="secao">
        TRIBUTOS FEDERAIS
    </div>

    <table class="tabela">

        <tr>

            <td width="20%">

                <span class="label">
                    IRRF
                </span>

                <span class="valor">
                    {{ $data['federal']['irrf'] ?? '-' }}
                </span>

            </td>

            <td width="30%">

                <span class="label">
                    Contribuição Previdenciária
                </span>

                <span class="valor">
                    {{ $data['federal']['contribuicao_previdenciaria'] ?? '-' }}
                </span>

            </td>

            <td width="20%">

                <span class="label">
                    CSLL
                </span>

                <span class="valor">
                    {{ $data['federal']['csll'] ?? '-' }}
                </span>

            </td>

            <td width="15%">

                <span class="label">
                    PIS
                </span>

                <span class="valor">
                    {{ $data['federal']['pis_debito'] ?? '-' }}
                </span>

            </td>

            <td width="15%">

                <span class="label">
                    COFINS
                </span>

                <span class="valor">
                    {{ $data['federal']['cofins_debito'] ?? '-' }}
                </span>

            </td>

        </tr>

        <tr>

            <td colspan="5">

                <span class="label">
                    Retenções
                </span>

                <span class="valor">
                    {{ $data['federal']['pis_cofins_csll'] ?? '-' }}
                </span>

            </td>

        </tr>

    </table>


    {{-- =====================================================
         IBS / CBS
    ====================================================== --}}

    <div class="secao">
        IBS / CBS
    </div>

    <table class="tabela">

        <tr>

            <td width="15%">

                <span class="label">
                    CST
                </span>

                <span class="valor-negrito">
                    {{ $data['ibs_cbs']['cst'] ?? '-' }}
                </span>

            </td>

            <td width="20%">

                <span class="label">
                    cClassTrib
                </span>

                <span class="valor">
                    {{ $data['ibs_cbs']['cclasstrib'] ?? '-' }}
                </span>

            </td>

            <td width="25%">

                <span class="label">
                    Indicador da Operação
                </span>

                <span class="valor">
                    {{ $data['ibs_cbs']['indicador_operacao'] ?? '-' }}
                </span>

            </td>

            <td width="40%">

                <span class="label">
                    Município de Incidência
                </span>

                <span class="valor">

                    {{ $data['ibs_cbs']['codigo_ibge_incidencia'] ?? '-' }}

                    -

                    {{ $data['ibs_cbs']['municipio_incidencia'] ?? '-' }}

                </span>

            </td>

        </tr>

        <tr>

            <td>

                <span class="label">
                    Base Original
                </span>

                <span class="valor">
                    {{ $data['ibs_cbs']['base_original'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Exclusões / Reduções
                </span>

                <span class="valor">
                    {{ $data['ibs_cbs']['exclusoes_reducoes'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Base de Cálculo
                </span>

                <span class="valor-negrito">
                    {{ $data['ibs_cbs']['base_calculo'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Reduções
                </span>

                <span class="texto">

                    UF:
                    {{ $data['ibs_cbs']['reducao_uf'] ?? '-' }}

                    &nbsp;&nbsp;

                    Mun:
                    {{ $data['ibs_cbs']['reducao_mun'] ?? '-' }}

                    &nbsp;&nbsp;

                    CBS:
                    {{ $data['ibs_cbs']['reducao_cbs'] ?? '-' }}

                </span>

            </td>

        </tr>

        <tr>

            <td>

                <span class="label">
                    Alíquota UF
                </span>

                <span class="valor">
                    {{ $data['ibs_cbs']['aliquota_uf'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Alíquota Município
                </span>

                <span class="valor">
                    {{ $data['ibs_cbs']['aliquota_mun'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Alíquota CBS
                </span>

                <span class="valor">
                    {{ $data['ibs_cbs']['aliquota_cbs'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Valores
                </span>

                <span class="texto">

                    IBS UF:
                    {{ $data['ibs_cbs']['ibs_uf'] ?? '-' }}

                    |

                    IBS Mun:
                    {{ $data['ibs_cbs']['ibs_mun'] ?? '-' }}

                    |

                    CBS:
                    {{ $data['ibs_cbs']['cbs'] ?? '-' }}

                </span>

            </td>

        </tr>

        <tr>

            <td colspan="3">

                <span class="label">
                    IBS Total
                </span>

                <span class="valor-total">
                    {{ $data['ibs_cbs']['ibs_total'] ?? '-' }}
                </span>

            </td>

            <td>

                <span class="label">
                    Total IBS + CBS
                </span>

                <span class="valor-total">
                    {{ $data['ibs_cbs']['total'] ?? '-' }}
                </span>

            </td>

        </tr>

    </table>


    {{-- =====================================================
         TOTAIS
    ====================================================== --}}

    <div class="secao">
        TOTAIS DA NFS-e
    </div>

    <table class="tabela">

        @php
            $totais = $data['totais'] ?? [];
        @endphp

        @if(count($totais))

            @foreach(array_chunk($totais, 3, true) as $grupo)

                <tr>

                    @foreach($grupo as $label => $valor)

                        <td width="33.33%" class="total-box">

                            <span class="label">
                                {{ ucwords(str_replace('_', ' ', $label)) }}
                            </span>

                            <span class="valor-negrito">
                                {{ $valor ?? '-' }}
                            </span>

                        </td>

                    @endforeach

                    @while(count($grupo) < 3)

                        <td width="33.33%">
                            &nbsp;
                        </td>

                        @php
                            $grupo[] = null;
                        @endphp

                    @endwhile

                </tr>

            @endforeach

        @else

            <tr>
                <td>
                    Nenhum total informado.
                </td>
            </tr>

        @endif

    </table>


    {{-- =====================================================
         INFORMAÇÕES COMPLEMENTARES
    ====================================================== --}}

    <div class="secao">
        INFORMAÇÕES COMPLEMENTARES
    </div>

    <table class="tabela">

        <tr>

            <td>

                <div class="informacoes">

                    {!! $data['informacoes_complementares'] ?? '-' !!}

                </div>

            </td>

        </tr>

    </table>


    {{-- =====================================================
         RODAPÉ
    ====================================================== --}}

    <div class="rodape">

        <table class="sem-borda">

            <tr>

                <td width="70%">

                    Documento auxiliar da NFS-e.

                </td>

                <td width="30%" class="direita">

                    NFS-e nº
                    {{ $data['header']['numero_nfse'] ?? '-' }}

                </td>

            </tr>

        </table>

    </div>

</div>

</body>
</html>