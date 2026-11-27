<x-area-cliente-layout title="Renovação de Licença de Uso">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Renovação de Licença de Uso') }}
        </h2>
    </x-slot>

    @php
        // Destaque: plano com maior economia (normalmente o anual)
        $idDestaque = optional($variacaoPlanos->sortByDesc('economia')->first(fn ($v) => $v->economia > 0.005))->id;
        $totalPlanos = $variacaoPlanos->count();
        $gridCols = $totalPlanos >= 4 ? 'lg:grid-cols-4' : ($totalPlanos == 3 ? 'lg:grid-cols-3' : 'lg:grid-cols-2');
    @endphp

    <div class="py-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Cabeçalho --}}
        <div class="bg-white overflow-hidden shadow-xl rounded-lg">
            <div class="px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-lg font-semibold text-gray-800">Renovação de Licença de Uso</h1>
                    <p class="mt-1 text-sm text-gray-500">Escolha o período ideal e renove em poucos cliques.</p>
                </div>

                <div class="flex items-center gap-3 bg-gray-50 rounded-lg px-4 py-3">
                    <div class="w-10 h-10 flex-shrink-0 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                        {{ mb_strtoupper(mb_substr($empresa->razao_social, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-gray-800 truncate">{{ $empresa->razao_social }}</div>
                        @if(!empty($empresa->cpf_cnpj))
                            <div class="text-xs text-gray-500">CPF/CNPJ {{ $empresa->cpf_cnpj_fmt ?? $empresa->cpf_cnpj }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @include('components.mensagens')

        {{-- Título do plano --}}
        <div class="text-center mt-12 mb-10">
            <span class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700 bg-blue-100 rounded-full">
                Seu plano
            </span>
            <h2 class="mt-3 font-bold text-3xl text-gray-800">{{ $plano->plano_nome }}</h2>
            <p class="mt-2 text-sm text-gray-500">Quanto maior o período, maior a economia em relação ao plano mensal.</p>
        </div>

        {{-- Cards dos planos --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 {{ $gridCols }} gap-6 items-stretch">
            @foreach($variacaoPlanos as $vPlano)
                @php
                    $destaque = $vPlano->id === $idDestaque;
                    $meses = max(1, (int) round($vPlano->fator_vigencia / 30));
                    $porMes = $vPlano->valor / $meses;
                @endphp

                <div class="plano-card relative flex flex-col bg-white rounded-2xl p-6
                            {{ $destaque ? 'plano-destaque border-2 border-blue-600 shadow-2xl' : 'border border-gray-200 shadow-md' }}">

                    @if($destaque)
                        <span class="plano-badge px-3 py-1 text-xs font-bold text-white rounded-full bg-gradient-to-r from-blue-600 to-teal-400 shadow">
                            Maior economia
                        </span>
                    @endif

                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                        Plano {{ $vPlano->descricao }}
                    </h3>

                    <div class="mt-3 flex items-baseline">
                        <span class="text-lg font-semibold text-gray-800 mr-1">R$</span>
                        <span class="text-4xl font-extrabold text-gray-900 leading-none">
                            {{ number_format($vPlano->valor, 2, ',', '.') }}
                        </span>
                    </div>

                    <p class="mt-2 text-sm text-gray-500">
                        @if($meses > 1)
                            equivale a <strong class="text-gray-700">R$ {{ number_format($porMes, 2, ',', '.') }}</strong> por mês
                        @else
                            por mês
                        @endif
                    </p>

                    <div class="mt-4 min-h-economia">
                        @if($vPlano->economia > 0.005)
                            <div class="rounded-lg bg-green-50 border border-green-200 px-3 py-2 text-sm font-semibold text-green-700">
                                {{ $vPlano->texto }}
                            </div>
                        @endif
                    </div>

                    <div class="mt-auto pt-6">
                        <a href="{{ route('licenca.gerar-fatura', base64_encode($vPlano->id)) }}"
                           class="plano-btn block w-full text-center px-4 py-3 rounded-full font-semibold text-base
                                  {{ $destaque
                                      ? 'text-white bg-gradient-to-r from-blue-600 to-teal-400 shadow-lg'
                                      : 'text-blue-700 border border-blue-600 hover:bg-blue-50' }}">
                            Renovar {{ $vPlano->descricao }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <p class="mt-10 text-center text-xs text-gray-500">
            Ao clicar em renovar, será gerada a fatura do período escolhido para pagamento.
        </p>
    </div>

    <style>
        .plano-card { transition: transform .2s ease, box-shadow .2s ease; }
        .plano-card:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0,0,0,.1), 0 10px 10px -5px rgba(0,0,0,.04); }
        .plano-badge { position: absolute; top: -0.8rem; left: 1.5rem; }
        .plano-btn { transition: filter .15s ease, background-color .15s ease; }
        .plano-btn:hover { filter: brightness(1.05); }
        .min-h-economia { min-height: 3.25rem; }
        @media (min-width: 1024px) {
            .plano-destaque { transform: scale(1.03); }
            .plano-destaque:hover { transform: scale(1.03) translateY(-4px); }
        }
    </style>

    @section('jquery')
    @endsection
</x-area-cliente-layout>
