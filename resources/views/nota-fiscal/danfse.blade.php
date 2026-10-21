<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>DANFSe</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #000;
            font-family: "Microsoft Sans Serif", Helvetica, sans-serif;
            font-size: 7pt;
            line-height: 1.08;
            margin: 0;
        }

        .page {
            height: 29.7cm;
            position: relative;
            width: 21cm;
        }

        .page::before {
            border: 1pt solid #000;
            content: "";
            height: 29.4cm;
            left: 0.15cm;
            position: absolute;
            top: 0.15cm;
            width: 20.70cm;
        }

        .print-area {
            height: 29.7cm;
            left: 0;
            position: absolute;
            top: 0;
            width: 21cm;
        }

        .logo {
            align-items: center;
            display: flex;
            height: 0.85cm;
            justify-content: center;
            left: 0.49cm;
            overflow: hidden;
            position: absolute;
            top: 0.44cm;
            width: 4cm;
        }

        .header-shade {
            background: #f2f2f2;
            border: 0.5pt solid #000;
            height: 1.16cm;
            left: 0.30cm;
            position: absolute;
            top: 0.30cm;
            width: 20.40cm;
        }

        .logo img {
            max-height: 0.85cm;
            max-width: 4cm;
        }

        .logo-fallback {
            color: #2f7d45;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 22pt;
            font-weight: 700;
            line-height: 0.85cm;
        }

        .logo-fallback span {
            color: #2e3d8f;
            font-size: 14pt;
        }

        .title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            font-weight: 700;
            height: 1.16cm;
            left: 5.41cm;
            line-height: 1.08;
            padding-top: 0.13cm;
            position: absolute;
            text-align: center;
            top: 0.30cm;
            width: 10.19cm;
        }

        .environment-notice {
            color: #f00;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            font-weight: 700;
            margin-top: 0.06cm;
        }

        .municipality {
            font-size: 8pt;
            height: 1.16cm;
            left: 15.62cm;
            overflow: hidden;
            padding-top: 0.05cm;
            position: absolute;
            text-align: center;
            top: 0.30cm;
            width: 5.09cm;
        }

        .municipality small {
            display: block;
            font-size: 6pt;
            line-height: 1.05;
            margin-top: 0.03cm;
        }

        .qr-code {
            height: 1.52cm;
            left: 17.48cm;
            position: absolute;
            top: 1.67cm;
            width: 1.52cm;
        }

        .qr-code img {
            height: 1.52cm;
            width: 1.52cm;
        }

        .qr-caption {
            font-size: 6pt;
            height: 0.68cm;
            left: 15.80cm;
            line-height: 1.08;
            overflow: hidden;
            position: absolute;
            text-align: center;
            top: 3.36cm;
            width: 4.72cm;
        }

        .cell,
        .message,
        .block-rule {
            overflow: hidden;
            position: absolute;
        }

        .cell {
            padding: 0.04cm 0.07cm;
        }

        .boxed {
            border: 0.5pt solid #000;
        }

        .block-rule {
            border-top: 0.5pt solid #000;
            height: 0;
        }

        .label {
            display: block;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 6pt;
            font-weight: 700;
            line-height: 1.02;
        }

        .document-field .label {
            font-size: 7pt;
            text-transform: uppercase;
        }

        .value {
            display: block;
            font-size: 7pt;
            line-height: 1.04;
            margin-top: 0.02cm;
            word-wrap: break-word;
        }

        .id-field .label {
            text-transform: uppercase;
        }

        .id-field .value {
            font-size: 7pt;
            font-weight: 700;
            line-height: 1.05;
        }

        .section-cell {
            background: #f2f2f2;
        }

        .section-cell .label {
            font-size: 7pt;
            line-height: 0.52cm;
            text-transform: uppercase;
        }

        .section-cell .value {
            display: none;
        }

        .highlight {
            background: #f2f2f2;
        }

        .no-label {
            padding-top: 0.05cm;
        }

        .no-label .label {
            display: none;
        }

        .no-label .value {
            margin-top: 0;
        }

        .large-text .value {
            font-size: 6.8pt;
            line-height: 1.08;
        }

        .message {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7pt;
            font-weight: 700;
            line-height: 0.32cm;
            text-align: center;
            text-transform: uppercase;
        }

        .watermark {
            color: rgba(0, 0, 0, 0.35);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 52pt;
            font-weight: 400;
            left: 2.4cm;
            position: absolute;
            text-align: center;
            top: 12.7cm;
            transform: rotate(-35deg);
            width: 16.5cm;
            z-index: 5;
        }
    </style>
</head>
<body>
@php
    $layout = \App\Services\NotaFiscalServico\DanfseLayout::build($data);
    $logoPath = public_path('img/nfse-logo-horizontal.png');
    $logoData = is_file($logoPath) ? base64_encode((string) file_get_contents($logoPath)) : null;
    $watermark = is_string($data['watermark'] ?? null) ? $data['watermark'] : null;
@endphp
<div class="page">
    <div class="print-area">
        <div class="header-shade"></div>

        <div class="logo">
            @if ($logoData)
                <img src="data:image/png;base64,{{ $logoData }}" alt="NFS-e">
            @else
                <div class="logo-fallback">NFS<span>e</span></div>
            @endif
        </div>

        <div class="title">
            DANFSe v2.0<br>
            Documento Auxiliar da NFS-e
            @if (!empty($data['environment_notice']))
                <div class="environment-notice">{{ $data['environment_notice'] }}</div>
            @endif
        </div>

        <div class="municipality">
            {{ $data['municipality_environment'] }}
            <small>Ambiente Gerador: {{ $data['environment_generator'] }}</small>
            <small>Tipo de Ambiente: {{ $data['environment_type'] }}</small>
        </div>

        <div class="qr-code">
            <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code DANFSe">
        </div>
        <div class="qr-caption">
            A autenticidade desta NFS-e pode ser verificada<br>
            pela leitura deste código QR ou pela consulta da<br>
            chave de acesso no portal nacional da NFS-e
        </div>

        @foreach ($layout['cells'] as $cell)
            <div
                class="cell {{ $cell['class'] }}"
                style="left: {{ number_format($cell['x'], 2, '.', '') }}cm; top: {{ number_format($cell['y'], 2, '.', '') }}cm; width: {{ number_format($cell['w'], 2, '.', '') }}cm; height: {{ number_format($cell['h'], 2, '.', '') }}cm;"
            >
                <span class="label">{{ $cell['label'] }}</span>
                <span class="value">{{ $cell['value'] }}</span>
            </div>
        @endforeach

        @foreach ($layout['messages'] as $message)
            <div
                class="message"
                style="left: {{ number_format($message['x'], 2, '.', '') }}cm; top: {{ number_format($message['y'], 2, '.', '') }}cm; width: {{ number_format($message['w'], 2, '.', '') }}cm; height: {{ number_format($message['h'], 2, '.', '') }}cm;"
            >
                {{ $message['text'] }}
            </div>
        @endforeach

        @foreach ($layout['rules'] as $top)
            <div
                class="block-rule"
                style="left: 0.30cm; top: {{ number_format($top, 2, '.', '') }}cm; width: 20.40cm;"
            ></div>
        @endforeach
    </div>

    @if ($watermark)
        <div class="watermark">{{ $watermark }}</div>
    @endif
</div>
</body>
</html>