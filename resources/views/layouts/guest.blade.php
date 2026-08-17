<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta name="description" content="Portal NFS-e sistema para emissão de nota eletrônica de serviço" />
        <meta name="keywords" content="nfse anapolis, nota eletronica anapolis, nfse, anapolis, nota eletronica, notaeletronica, NFSE, NFS-E, NFE, NF-E, NFCE, NFC-E, nota fiscal de servicos eletronica, nota fiscal de servico, portalnfse, portal nfse, iss, ISS, issqn prefeitura online, evollucao, sistema iss, sistema issqn, gestao iss, gestao issqn, aparecida de goiania, sao vicente, certificado digital, cert digital, Certificado A1, Certificado A3" />

        <title>{{ config('app.name', 'Laravel') }} - Emissor de Nota Fiscal - NFS-e, NFC-e, NF-e</title>
        <link rel="icon" href="{{ asset('img/favicon.png') }}" />

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <meta name="facebook-domain-verification" content="ezyj667w2wmbzyt4s9td4z1pe2hge5" />

        <meta property="og:title" content="Portal Nota Fácil - Emissor de NFS-e" />
        <meta property="og:description" content="Sistema emissor de NFS-e padrão Nacional e ABRASF." />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="https://portalnotafacil.com.br" />
        <meta property="og:image" content="https://portalnotafacil.com.br/principal/assets/img/logo.png" />

        <link rel="canonical" href="{{ url()->current() }}" />

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.2.1/dist/alpine.js" defer></script>
        <script src="{{ asset('js/app.js') }}"></script>
        <script type="application/ld+json">
        {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "Portal Nota Fácil",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "description": "Sistema emissor de NFS-e padrão Nacional e ABRASF.",
        "offers": {
            "@type": "Offer",
            "price": "7.90",
            "priceCurrency": "BRL"
        }
        }
        </script>
    </head>
    <body>
        <div class="font-sans antialiased text-gray-900">
            {{ $slot }}
        </div>
    </body>
</html>
