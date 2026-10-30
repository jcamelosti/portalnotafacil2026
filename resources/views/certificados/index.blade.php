<x-area-cliente-layout title="Empresas">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Empresas') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto w-full sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Certificados Digitais
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Listagem
                </p>
            </h1>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                    <a href="{{ route('empresas-certificados.create') }}"
                        class="inline-flex justify-center w-full rounded-md border border-blue-700 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                        Adicionar
                    </a>
                </span>
            </div>

            <div class="px-4 py-3 mb-8 bg-white ">
                {!! Form::open(['route' => 'empresas-certificados.index', 'method' => 'GET']) !!}
                <label class="block text-sm">
                    <span class="text-gray-700 ">Empresa:</span>
                    {!! Form::select('empresa_id', $empresasList, isset($pesquisa['empresa_id']) ? $pesquisa['empresa_id'] : null, ['class' => 'select2  block w-full mt-1 text-sm    form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                    @if ($errors->has('empresa_id'))
                        <span class="text-xs text-red-600 ">
                            <strong>{{ $errors->first('empresa_id') }}</strong>
                        </span>
                    @endif
                </label>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                        <button type="submit" type="button"
                            class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            Pesquisar
                        </button>
                    </span>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        @include('components.mensagens')

        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mt-6">

            @forelse ($certificados as $item)

                <div class="bg-white rounded-xl shadow-md border border-gray-200
                            hover:shadow-lg transition-shadow duration-200
                            flex flex-col overflow-hidden">

                    {{-- Cabeçalho --}}
                    <div class="p-4 flex-1">

                        <div class="flex items-start justify-between gap-2 mb-3">

                            <div class="flex items-center justify-center
                                        w-10 h-10 rounded-lg bg-blue-100
                                        text-blue-600 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6M7 3h7l5 5v13H5V3h2z"/>
                                </svg>
                            </div>

                            @if ($item->vencimento_valido)
                                <span class="inline-flex items-center px-2 py-1
                                            rounded-full bg-green-100
                                            text-green-700 text-xs font-semibold">
                                    Válido
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1
                                            rounded-full bg-red-100
                                            text-red-700 text-xs font-semibold">
                                    Expirado
                                </span>
                            @endif

                        </div>

                        {{-- Razão Social --}}
                        <h3 class="text-base font-bold text-gray-800
                                leading-snug mb-3 break-words">
                            {{ $item->razao_social }}
                        </h3>

                        {{-- CPF/CNPJ --}}
                        <div class="mb-3">
                            <p class="text-xs text-gray-500 mb-1">
                                CPF / CNPJ
                            </p>
                            <p class="text-sm font-semibold text-gray-700">
                                {{ $item->cpf_cnpj }}
                            </p>
                        </div>

                        {{-- Validade --}}
                        <div>
                            <p class="text-xs text-gray-500 mb-1">
                                Data de validade
                            </p>

                            <p class="text-sm font-semibold
                                {{ $item->vencimento_valido
                                    ? 'text-green-700'
                                    : 'text-red-600' }}">
                                {{ $item->data_validade->format('d/m/Y') }}
                            </p>
                        </div>

                    </div>

                    {{-- Rodapé --}}
                    <div class="border-t border-gray-100 bg-gray-50
                                px-4 py-3">

                        <a href="{{ route('empresas-certificados.edit', $item->id) }}"
                        class="inline-flex items-center justify-center
                                w-full px-4 py-2 rounded-lg
                                bg-blue-600 hover:bg-blue-700
                                text-white text-sm font-medium
                                transition-colors duration-200">

                            Editar certificado
                        </a>

                    </div>

                </div>

            @empty

                <div class="col-span-full bg-white rounded-xl
                            shadow-sm border border-gray-200
                            text-center py-12 px-4">

                    <p class="text-gray-500 text-sm">
                        Não há certificados digitais para exibir.
                    </p>

                </div>

            @endforelse

        </div>

    </div>

    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        {!! $certificados->links('pagination') !!}
    </div>
</x-area-cliente-layout>
