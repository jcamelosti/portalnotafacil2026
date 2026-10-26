<x-app-layout title="Dashboard">

    <div class="min-h-screen w-full bg-gray-50 px-4 py-6 sm:px-6 lg:px-8">

        {{-- CABEÇALHO --}}
        <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="mb-1 text-sm font-medium text-blue-600">
                    PAINEL DO CLIENTE
                </p>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                    Área do Cliente
                </h1>
                <p class="mt-2 text-sm text-gray-500">
                    Gerencie suas empresas, licenças e serviços em um só lugar.
                </p>
            </div>

            <a href="{{ route('empresas.adicionar') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 hover:shadow-md">
                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Adicionar empresa
            </a>
        </div>

        {{-- MENSAGENS DO SISTEMA --}}
        @include('components.mensagens')

       
        {{-- RESUMO --}}
        <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">

            {{-- CRÉDITO EM CONTA --}}
            @if(auth()->user()->can_insert_credit == 1)
                <div class="relative overflow-hidden rounded-2xl shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                    style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">

                    <div class="absolute -right-5 -top-7 h-28 w-28 rounded-full"
                        style="background: rgba(255,255,255,0.10);"></div>

                    <div class="relative p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold"
                                style="color: #d1fae5;">
                                    Crédito em conta
                                </p>

                                <p class="mt-2 text-2xl font-extrabold"
                                style="color: #ffffff;">
                                    R$ {{ number_format(optional($credito)->credito ?? 0, 2, ',', '.') }}
                                </p>
                            </div>

                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl"
                                style="background: rgba(255,255,255,0.18); color: #ffffff;">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 2v8m0 0v2m8-6a8 8 0 11-16 0 8 8 0 0116 0z"/>
                                </svg>
                            </div>
                        </div>

                        <div class="mt-5 border-t pt-3"
                            style="border-color: rgba(255,255,255,0.25);">
                            <span class="text-xs font-semibold"
                                style="color: #d1fae5;">
                                ✓ Saldo disponível
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- EMPRESAS CADASTRADAS --}}
            <div class="relative overflow-hidden rounded-2xl shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">

                <div class="absolute -right-5 -top-7 h-28 w-28 rounded-full"
                    style="background: rgba(255,255,255,0.10);"></div>

                <div class="relative p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold"
                            style="color: #e0e7ff;">
                                Empresas cadastradas
                            </p>

                            <p class="mt-2 text-3xl font-extrabold"
                            style="color: #ffffff;">
                                {{ $empresas->count() }}
                            </p>
                        </div>

                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl"
                            style="background: rgba(255,255,255,0.18); color: #ffffff;">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 21h18M5 21V7l8-4v18m6 0V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01M14 9v.01M14 12v.01M14 15v.01"/>
                            </svg>
                        </div>
                    </div>

                    <div class="mt-5 border-t pt-3"
                        style="border-color: rgba(255,255,255,0.25);">
                        <span class="text-xs font-semibold"
                            style="color: #e0e7ff;">
                            Gestão empresarial
                        </span>
                    </div>
                </div>
            </div>

            {{-- ACESSOS PENDENTES --}}
            @if($quantCompartilhamentosSolicitados > 0)
                <div class="relative overflow-hidden rounded-2xl shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                    style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);">

                    <div class="absolute -right-5 -top-7 h-28 w-28 rounded-full"
                        style="background: rgba(255,255,255,0.10);"></div>

                    <div class="relative p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold"
                                style="color: #ffedd5;">
                                    Acessos pendentes
                                </p>

                                <p class="mt-2 text-3xl font-extrabold"
                                style="color: #ffffff;">
                                    {{ $quantCompartilhamentosSolicitados }}
                                </p>
                            </div>

                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl"
                                style="background: rgba(255,255,255,0.18); color: #ffffff;">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.196-2.045M17 20H7m10 0v-2c0-.728-.195-1.411-.536-2M7 20H2v-2a3 3 0 015.196-2.045M7 20v-2c0-.728.195-1.411.536-2m0 0a5.002 5.002 0 019.928 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>

                        <div class="mt-5 border-t pt-3"
                            style="border-color: rgba(255,255,255,0.25);">
                            <a href="{{ route('empresas.list-solicitar-acesso-empresa') }}"
                            class="inline-flex items-center gap-2 text-sm font-bold"
                            style="color: #ffffff;">
                                Ver solicitações
                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- LICENÇA --}}
        @if($empresas->count() == 1)

            @if(!$temLicencaValida)

                {{-- LICENÇA INATIVA --}}
                <div class="mb-8 overflow-hidden rounded-2xl shadow-sm"
                    style="background-color: #ffffff; border: 1px solid #fecaca;">

                    <div class="flex items-center gap-3 px-5 py-4"
                        style="background-color: #fef2f2; border-bottom: 1px solid #fecaca;">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl"
                            style="background-color: #fee2e2; color: #dc2626;">

                            <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-bold"
                                style="color: #991b1b;">
                                Atenção: licença inativa
                            </h3>

                            <p class="text-sm"
                            style="color: #dc2626;">
                                É necessário renovar sua licença.
                            </p>
                        </div>

                        <span class="ml-auto rounded-full px-3 py-1 text-xs font-bold"
                            style="background-color: #fee2e2; color: #991b1b;">
                            INATIVA
                        </span>
                    </div>

                    <div class="flex flex-col justify-between gap-4 p-5 sm:flex-row sm:items-center">

                        <p class="text-sm leading-6"
                        style="color: #4b5563;">
                            Para continuar emitindo suas Notas Fiscais de Serviços,
                            renove sua licença.
                        </p>

                        <a href="{{ route('licenca.renovacao', ['id' => $empresa->id]) }}"
                        class="inline-flex shrink-0 items-center justify-center rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:opacity-90"
                        style="background-color: #dc2626; color: #ffffff;">
                            Renovar licença
                        </a>

                    </div>
                </div>

            @elseif($license)

                {{-- LICENÇA ATIVA --}}
                <div class="mb-8 overflow-hidden rounded-2xl shadow-sm"
                    style="background-color: #ffffff; border: 1px solid #a7f3d0;">

                    <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-3">

                            <div class="flex h-12 w-12 items-center justify-center rounded-xl"
                                style="background-color: #d1fae5; color: #059669;">

                                <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>

                            <div>
                                <h3 class="font-bold"
                                    style="color: #065f46;">
                                    Licença ativa
                                </h3>

                                <p class="mt-1 text-sm"
                                style="color: #6b7280;">
                                    Sua licença está ativada e pronta para uso.
                                </p>
                            </div>

                        </div>

                        <div class="rounded-xl px-5 py-3"
                            style="background-color: #ecfdf5; border: 1px solid #a7f3d0;">

                            <p class="text-xs font-semibold"
                            style="color: #047857;">
                                VENCIMENTO DA LICENÇA
                            </p>

                            <p class="mt-1 text-lg font-bold"
                            style="color: #065f46;">
                                {{ $license->validate_pt_br }}
                            </p>

                        </div>

                        <span class="inline-flex items-center gap-2 self-start rounded-full px-3 py-1 text-xs font-bold sm:self-center"
                            style="background-color: #d1fae5; color: #065f46;">

                            <span class="h-2 w-2 rounded-full"
                                style="background-color: #059669;"></span>

                            ATIVA
                        </span>

                    </div>
                </div>

            @endif
        @else
            empresas com licença vencendo
        @endif

        {{-- CERTIFICADO DIGITAL — PROMOÇÃO POR TEMPO LIMITADO --}}
        <div class="relative mb-10 overflow-hidden rounded-2xl shadow-lg"
            style="background: linear-gradient(135deg, #064e3b 0%, #047857 55%, #16a34a 100%);">

            {{-- ELEMENTOS DECORATIVOS --}}
            <div class="absolute -right-10 -top-10 h-48 w-48 rounded-full"
                style="background: rgba(255,255,255,0.08);"></div>

            <div class="absolute -bottom-16 right-32 h-48 w-48 rounded-full"
                style="background: rgba(255,255,255,0.08);"></div>

            <div class="relative flex flex-col gap-6 p-6 sm:p-8 lg:flex-row lg:items-center lg:justify-between">

                {{-- TEXTO DA OFERTA --}}
                <div class="max-w-2xl">

                    <div class="mb-3 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold"
                        style="background: #fef3c7; color: #92400e;">

                        <span>🔥</span>
                        OFERTA ESPECIAL POR TEMPO LIMITADO
                    </div>

                    <h2 class="text-2xl font-bold sm:text-3xl"
                        style="color: #ffffff;">
                        Emita seu Certificado Digital por um preço especial!
                    </h2>

                    <p class="mt-3 text-sm leading-6 sm:text-base"
                    style="color: #ecfdf5;">
                        Aproveite esta oportunidade para emitir seu certificado digital
                        com praticidade, atendimento seguro e emissão por videoconferência.
                        Resolva tudo com o suporte da nossa equipe!
                    </p>

                    {{-- PREÇO PROMOCIONAL --}}
                    <div class="mt-5 inline-block rounded-2xl px-5 py-4"
                        style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25);">

                        <p class="text-xs font-bold uppercase tracking-wider"
                        style="color: #d1fae5;">
                            Preço promocional
                        </p>

                        <div class="mt-1 flex flex-wrap items-baseline gap-2">
                            <span class="text-4xl font-extrabold sm:text-5xl"
                                style="color: #ffffff;">
                                R$ 180,00
                            </span>
                        </div>

                        <p class="mt-2 text-sm font-semibold"
                        style="color: #fef3c7;">
                            ⏳ Promoção válida até 31/12/2026!
                        </p>
                    </div>

                    <p class="mt-4 text-sm font-medium"
                    style="color: #ffffff;">
                        Não deixe para a última hora. Aproveite o valor promocional e entre em contato!
                    </p>
                </div>

                {{-- BOTÃO WHATSAPP --}}
                <div class="flex shrink-0 flex-col items-stretch gap-3 sm:items-start lg:items-center">

                    <a href="https://wa.me/5562984018589?text=Ol%C3%A1%21%20Tenho%20interesse%20na%20promo%C3%A7%C3%A3o%20do%20Certificado%20Digital%20por%20R%24%20180%2C00."
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2 rounded-xl px-6 py-4 text-base font-extrabold shadow-lg transition hover:opacity-90"
                    style="background: #ffffff; color: #047857;">

                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.52 3.48A11.8 11.8 0 0012.12 0C5.58 0 .25 5.33.25 11.87c0 2.09.55 4.13 1.59 5.94L.15 24l6.34-1.66a11.88 11.88 0 005.63 1.43h.01c6.54 0 11.87-5.33 11.87-11.87a11.8 11.8 0 00-3.48-8.42z"/>
                        </svg>

                        QUERO APROVEITAR!
                    </a>

                    <p class="text-center text-xs"
                    style="color: #d1fae5;">
                        Fale agora com nossa equipe pelo WhatsApp.
                    </p>
                </div>

            </div>
        </div>

        {{-- GESTÃO DE EMPRESAS --}}
        <div class="mb-10">

            {{-- CABEÇALHO DA SEÇÃO --}}
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl"
                    style="background-color: #4f46e5; color: #ffffff;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 21h18M5 21V7l8-4v18m6 0V11l-6-4"/>
                    </svg>
                </div>

                <div>
                    <h2 class="text-xl font-bold" style="color: #312e81;">
                        Gestão de Empresas
                    </h2>
                    <p class="mt-1 text-sm" style="color: #6b7280;">
                        Gerencie seus cadastros e mantenha suas empresas organizadas.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                {{-- CARD ADICIONAR EMPRESA --}}
                <div class="group overflow-hidden rounded-2xl shadow-md transition duration-300 hover:-translate-y-1 hover:shadow-xl"
                    style="background-color: #ffffff; border: 1px solid #bfdbfe;">

                    {{-- FAIXA COLORIDA --}}
                    <div class="h-2" style="background: linear-gradient(90deg, #2563eb, #4f46e5);"></div>

                    <div class="p-6">
                        <div class="mb-5 flex items-center justify-between">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl"
                                style="background-color: #dbeafe; color: #1d4ed8;">

                                <svg class="h-7 w-7" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>

                            <span class="rounded-full px-3 py-1 text-xs font-bold"
                                style="background-color: #eff6ff; color: #1d4ed8;">
                                CADASTRO
                            </span>
                        </div>

                        <h3 class="text-lg font-bold" style="color: #1e3a8a;">
                            Adicionar empresa
                        </h3>

                        <p class="mt-2 text-sm leading-6" style="color: #6b7280;">
                            Cadastre uma nova empresa e gerencie suas informações,
                            licenças e emissão de notas fiscais de forma individual.
                        </p>

                        <div class="mt-6 border-t pt-4" style="border-color: #dbeafe;">
                            <a href="{{ route('empresas.adicionar') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl px-5 py-3 text-sm font-bold transition hover:opacity-90 sm:w-auto"
                            style="background-color: #2563eb; color: #ffffff;">

                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>

                                Cadastrar empresa

                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- CARD SOLICITAÇÕES DE ACESSO --}}
                @if($quantCompartilhamentosSolicitados > 0)
                    <div class="group overflow-hidden rounded-2xl shadow-md transition duration-300 hover:-translate-y-1 hover:shadow-xl"
                        style="background-color: #ffffff; border: 1px solid #fed7aa;">

                        {{-- FAIXA COLORIDA --}}
                        <div class="h-2" style="background: linear-gradient(90deg, #ea580c, #f59e0b);"></div>

                        <div class="p-6">
                            <div class="mb-5 flex items-center justify-between">
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl"
                                    style="background-color: #ffedd5; color: #c2410c;">

                                    <svg class="h-7 w-7" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.196-2.045M17 20H7m10 0v-2c0-.728-.195-1.411-.536-2M7 20H2v-2a3 3 0 015.196-2.045M7 20v-2c0-.728.195-1.411.536-2m0 0a5.002 5.002 0 019.928 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>

                                <span class="rounded-full px-3 py-1 text-xs font-bold"
                                    style="background-color: #fef3c7; color: #92400e;">
                                    {{ $quantCompartilhamentosSolicitados }} PENDENTE(S)
                                </span>
                            </div>

                            <h3 class="text-lg font-bold" style="color: #9a3412;">
                                Solicitações de acesso
                            </h3>

                            <p class="mt-2 text-sm leading-6" style="color: #6b7280;">
                                Existem solicitações de compartilhamento de empresas
                                aguardando sua consulta. Acesse para verificar os pedidos.
                            </p>

                            <div class="mt-6 border-t pt-4" style="border-color: #ffedd5;">
                                <a href="{{ route('empresas.list-solicitar-acesso-empresa') }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl px-5 py-3 text-sm font-bold transition hover:opacity-90 sm:w-auto"
                                style="background-color: #ea580c; color: #ffffff;">

                                    Ver solicitações

                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>


        {{-- SUPORTE TÉCNICO --}}
        <div class="relative mb-8 overflow-hidden rounded-2xl shadow-sm"
            style="background-color: #ffffff; border: 1px solid #d1fae5;">

            {{-- CABEÇALHO --}}
            <div class="flex items-center gap-4 px-6 py-5"
                style="background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); border-bottom: 1px solid #d1fae5;">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"
                    style="background-color: #059669; color: #ffffff;">

                    <svg class="h-6 w-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M18 10a6 6 0 00-12 0v4a2 2 0 002 2h1v-5H8a4 4 0 018 0h-1v5h1a2 2 0 002-2v-4zM18 16v1a3 3 0 01-3 3h-3"/>
                    </svg>
                </div>

                <div>
                    <h2 class="text-xl font-bold"
                        style="color: #065f46;">
                        Precisa de ajuda?
                    </h2>

                    <p class="mt-1 text-sm"
                    style="color: #047857;">
                        Nossa equipe está pronta para ajudar você.
                    </p>
                </div>

                <span class="ml-auto hidden rounded-full px-3 py-1 text-xs font-bold sm:inline-flex"
                    style="background-color: #d1fae5; color: #065f46;">
                    SUPORTE
                </span>
            </div>

            {{-- CONTEÚDO --}}
            <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-start gap-4">

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"
                        style="background-color: #dcfce7; color: #15803d;">

                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.52 3.48A11.8 11.8 0 0012.12 0C5.58 0 .25 5.33.25 11.87c0 2.09.55 4.13 1.59 5.94L.15 24l6.34-1.66a11.88 11.88 0 005.63 1.43h.01c6.54 0 11.87-5.33 11.87-11.87a11.8 11.8 0 00-3.48-8.42zM12.13 21.75a9.9 9.9 0 01-5.04-1.38l-.36-.21-3.76.98 1-3.66-.24-.38a9.84 9.84 0 01-1.51-5.23c0-5.45 4.44-9.89 9.9-9.89a9.82 9.82 0 016.99 2.9 9.82 9.82 0 012.89 6.99c0 5.45-4.44 9.88-9.87 9.88z"/>
                        </svg>
                    </div>

                    <div>
                        <h3 class="text-base font-bold"
                            style="color: #111827;">
                            Suporte técnico
                        </h3>

                        <p class="mt-1 text-sm leading-6"
                        style="color: #6b7280;">
                            Atendimento de segunda a sexta-feira,
                            das 08h às 17h.
                        </p>

                        <p class="mt-2 text-sm font-semibold"
                        style="color: #374151;">
                            (62) 98401-8589
                        </p>

                        <div class="mt-3 inline-flex items-center gap-2 text-xs font-medium"
                            style="color: #059669;">
                            <span class="h-2 w-2 rounded-full"
                                style="background-color: #10b981;"></span>
                            Atendimento via WhatsApp
                        </div>
                    </div>
                </div>

                {{-- BOTÃO WHATSAPP --}}
                <a href="https://wa.me/5562984018589?text=Ol%C3%A1%2C%20preciso%20de%20suporte%20t%C3%A9cnico"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl px-6 py-3 text-sm font-bold shadow-sm transition hover:opacity-90"
                style="background-color: #16a34a; color: #ffffff;">

                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.52 3.48A11.8 11.8 0 0012.12 0C5.58 0 .25 5.33.25 11.87c0 2.09.55 4.13 1.59 5.94L.15 24l6.34-1.66a11.88 11.88 0 005.63 1.43h.01c6.54 0 11.87-5.33 11.87-11.87a11.8 11.8 0 00-3.48-8.42zM12.13 21.75a9.9 9.9 0 01-5.04-1.38l-.36-.21-3.76.98 1-3.66-.24-.38a9.84 9.84 0 01-1.51-5.23c0-5.45 4.44-9.89 9.9-9.89a9.82 9.82 0 016.99 2.9 9.82 9.82 0 012.89 6.99c0 5.45-4.44 9.88-9.87 9.88z"/>
                    </svg>

                    Falar com o suporte

                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

            </div>
        </div>


        <p class="mt-8 text-center text-xs text-gray-400">
            Área do Cliente · Gestão de Notas Fiscais de Serviços
        </p>

    </div>
</x-app-layout>