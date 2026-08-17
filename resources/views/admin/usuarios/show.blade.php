<x-area-admin-layout title="Empresas por Usuário">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Empresas por Usuário') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto w-full sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Empresas
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Listagem das Empresas
                </p>
            </h1>
        </div>

        @include('components.mensagens')

        {{-- CARD DO USUÁRIO --}}
        <div class="mt-6 bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    {{-- INFO DO USUÁRIO --}}
                    <div class="flex items-center gap-4">

                        {{-- AVATAR --}}
                        <!--div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
                            { { strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div-->
                        <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>

                        {{-- DADOS --}}
                        <div>
                            <div class="text-lg font-bold text-gray-800">
                                {{ $user->name }}
                            </div>

                            <div class="text-sm text-gray-500">
                                {{ $user->email }}
                            </div>
                        </div>
                    </div>

                    {{-- ESTATÍSTICAS --}}
                    <div class="flex gap-6">

                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">
                                {{ $empresas->total() }}
                            </div>
                            <div class="text-xs text-gray-500 uppercase">
                                Empresas
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>

        <div class="container-list">
            @forelse ($empresas as $empresa)
                <div
                    class="
                        flex flex-col
                        items-center
                        p-2
                        bg-gray-50
                        shadow-md
                        m-1
                        w-full
                    ">

                    {{-- LINHA PRINCIPAL --}}
                    <div class="flex flex-row items-center justify-between w-full">

                        {{-- NOME --}}
                        <div
                            class="
                                flex
                                items-center
                                flex-grow
                                font-bold
                                text-base
                                md:text-2xl
                            ">
                            {{ $empresa->razao_social }}
                        </div>
                    </div>

                    {{-- LINHA SECUNDÁRIA --}}
                    <div class="flex flex-row items-start justify-between w-full">
                        <div class="flex inline-block">

                            {{-- CNPJ (opcional) --}}
                            @if(isset($empresa->cpf_cnpj))
                                <span
                                    class="
                                        rounded rounded-lg
                                        bg-blue-500
                                        text-white text-xs
                                        px-2
                                        ml-1
                                        uppercase
                                        font-bold
                                    ">
                                    {{ $empresa->cpf_cnpj }}
                                </span>
                            @endif

                            @if(isset($empresa->validate_licenca))
                                @php
                                    $dataLicenca = \Carbon\Carbon::parse($empresa->validate_licenca);
                                    $hoje = now();

                                    $classeCor = 'bg-green-500 text-white'; // válida

                                    if ($dataLicenca->isPast()) {
                                        $classeCor = 'bg-red-600 text-white'; // vencida
                                    } elseif ($dataLicenca->diffInDays($hoje, false) >= -30) {
                                        $classeCor = 'bg-yellow-400 text-yellow-900'; // vence em até 30 dias
                                    }
                                @endphp

                                <span class="rounded rounded-lg text-xs px-2 ml-1 font-bold {{ $classeCor }}">
                                    {{ $dataLicenca->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 text-gray-500">
                    Não há empresas para exibir.
                </div>
            @endforelse
        </div>
    </div>

    <div class="py-0 mx-auto w-full sm:px-6 lg:px-8">
        {!! $empresas->links('pagination') !!}
    </div>

</x-area-admin-layout>