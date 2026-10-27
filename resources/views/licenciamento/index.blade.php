<x-area-cliente-layout title="Empresas">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01M15 12v.01M15 15v.01M15 18v.01" />
                </svg>
            </div>

            <div>
                <h2 class="text-xl font-bold leading-tight text-gray-800">
                    Empresas
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Gerencie suas empresas e acompanhe as licenças.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Filtro de pesquisa --}}
        <div class="mb-8 overflow-visible rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4 sm:px-6">
                <h3 class="text-base font-semibold text-gray-800">
                    Filtrar empresas
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Selecione uma empresa para localizar sua licença.
                </p>
            </div>

            <div class="p-5 sm:p-6">
                {!! Form::open(['route' => 'licenca.index', 'method' => 'GET']) !!}

                <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-[1fr_auto]">
                    <label class="block text-sm font-medium text-gray-700">
                        Empresa

                        {!! Form::select(
                            'empresa_id',
                            $empresasList,
                            request()->empresa_id ?? null,
                            [
                                'class' => 'select2 mt-2 block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                                'placeholder' => 'Todas as empresas'
                            ]
                        ) !!}

                        @if ($errors->has('empresa_id'))
                            <span class="mt-1 block text-xs text-red-600">
                                {{ $errors->first('empresa_id') }}
                            </span>
                        @endif
                    </label>

                    <button type="submit"
                        class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-4.35-4.35M19 10.5a8.5 8.5 0 11-17 0 8.5 8.5 0 0117 0z" />
                        </svg>
                        Pesquisar
                    </button>
                </div>

                {!! Form::close() !!}
            </div>
        </div>

        {{-- Mensagens do sistema --}}
        @include('components.mensagens')

        {{-- Cabeçalho da listagem --}}
        <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">
                    Licenças das empresas
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Consulte a situação e acesse a renovação de cada licença.
                </p>
            </div>

            <span class="inline-flex w-fit items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                {{ $vencidas->total() }} registro(s)
            </span>
        </div>

        {{-- Grid de cards --}}
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($vencidas as $item)
                @php
                    $validade = $item->validate_licenca ?? $item->validate;
                    $temValidade = !empty($validade);
                    $estaVencida = $temValidade
                        ? \Carbon\Carbon::parse($validade)->endOfDay()->isPast()
                        : true;

                    $integrado = !empty($item->id_integracao);
                @endphp

                <article class="group flex min-w-0 flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">

                    {{-- Faixa de status --}}
                    <div class="h-1.5 {{ $estaVencida ? 'bg-red-500' : 'bg-emerald-500' }}"></div>

                    <div class="flex flex-1 flex-col p-5">

                        {{-- Empresa e integração --}}
                        <div class="mb-4 flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-start gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $estaVencida ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01" />
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <h4 class="break-words text-base font-bold leading-6 text-gray-900">
                                        {{ $item->empresa->razao_social ?? 'Empresa não identificada' }}
                                    </h4>

                                    <p class="mt-1 break-all text-sm text-gray-500">
                                        {{ $item->empresa->cpf_cnpj ?? $item->cpf_cnpj ?? 'CNPJ não informado' }}
                                    </p>
                                </div>
                            </div>

                            @if ($integrado)
                                <span title="Integração ativa"
                                    class="inline-flex shrink-0 items-center gap-1 rounded-full bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                    Integrada
                                </span>
                            @endif
                        </div>

                        {{-- Situação da licença --}}
                        <div class="mt-auto rounded-xl {{ $estaVencida ? 'bg-red-50' : 'bg-green-100' }} p-4">
                            <div class="flex items-center gap-2">
                                @if ($estaVencida)
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 shrink-0 text-red-600"
                                        fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 9v4m0 4h.01M10.3 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.7 3.86a2 2 0 00-3.4 0z" />
                                    </svg>
                                    <span class="text-sm font-bold text-red-700">
                                        Licença vencida
                                    </span>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 shrink-0 text-emerald-600"
                                        fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm font-bold text-emerald-700">
                                        Licença válida
                                    </span>
                                @endif
                            </div>

                            <p class="mt-2 text-sm {{ $estaVencida ? 'text-red-600' : 'text-emerald-700' }}">
                                @if ($temValidade)
                                    {{ $estaVencida ? 'Vencimento em' : 'Válida até' }}
                                    <span class="font-bold">
                                        {{ \Carbon\Carbon::parse($validade)->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="font-medium">
                                        Data de validade não informada
                                    </span>
                                @endif
                            </p>
                        </div>

                        {{-- Ação --}}
                        <div class="mt-5 border-t border-gray-100 pt-4">
                            <a href="{{ route('licenca.renovacao', ['id' => $item->id]) }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl {{ $estaVencida ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500' }} px-4 py-3 text-sm font-semibold text-white transition focus:outline-none focus:ring-2 focus:ring-offset-2">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 4v5h.582m14.836 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-14.836-2m14.836 2H15" />
                                </svg>
                                Renovar licença
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border-2 border-dashed border-gray-200 bg-white px-6 py-14 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M9 13h6m-6 4h6m2 4H7a2 2 0 01-2-2V5a2 2 0 012-2h6l6 6v10a2 2 0 01-2 2z" />
                        </svg>
                    </div>

                    <h3 class="mt-4 text-base font-semibold text-gray-900">
                        Nenhuma empresa encontrada
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Não existem registros para os filtros selecionados.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Paginação --}}
        @if ($vencidas->hasPages())
            <div class="mt-8">
                {!! $vencidas->links('pagination') !!}
            </div>
        @endif
    </div>
</x-area-cliente-layout>
