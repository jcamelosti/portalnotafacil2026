<x-area-admin-layout title="Empresas">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Empresas') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto w-full sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Empresas
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Listagem
                </p>
            </h1>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                    <a href="{{ route('admin.empresas.create') }}"
                        class="inline-flex justify-center w-full rounded-md border border-blue-700 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                        Adicionar
                    </a>
                </span>
            </div>

            <div class="px-4 py-3 mb-8 bg-white ">
                {!! Form::open(['route' => 'admin.empresas.index', 'method' => 'GET']) !!}
                <label class="block text-sm">
                    <span class="text-gray-700 ">Empresa:</span>
                    {!! Form::select('empresa_id', $empresasList, isset($pesquisa['empresa_id']) ? $pesquisa['empresa_id'] : null, ['class' => 'select2  block w-full mt-1 text-sm    form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                    @if ($errors->has('empresa_id'))
                        <span class="text-xs text-red-600 ">
                            <strong>{{ $errors->first('empresa_id') }}</strong>
                        </span>
                    @endif
                </label>
                <label class="block text-sm">
                    <span class="text-gray-700 ">Cidade Atendidas:</span>
                   
                    <select name="cidade_id" class="block w-full mt-1 text-sm form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray">
                    <option value="">Selecione o Município</option>    
                    @foreach ($cidades as $cidade)
                        @if($cidade->total_empresas > 0)
                        <option {{ old('cidade_id', request()->cidade_id) == $cidade->codigo ? ' selected ' : '' }} value="{{ $cidade->codigo }}">{{ $cidade->municipio}} ({{ $cidade->total_empresas }})</option>
                        @endif
                    @endforeach
                    </select>
                </label>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                     <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto ml-2">
                        <a href="{{ route('admin.empresas.index') }}"
                        class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                           Limpar Pesquisa
                        </a>
                    </span>
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

        
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mt-5">

            @forelse ($empresas as $item)

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition duration-200 overflow-hidden">

                    {{-- Cabeçalho do card --}}
                    <div class="p-5">

                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">
                                <h3 class="text-base font-bold text-gray-800 leading-tight">
                                    {{ $item->razao_social }}
                                </h3>

                                <p class="text-sm text-gray-500 mt-2">
                                    CNPJ / CPF
                                </p>

                                <p class="text-sm font-semibold text-gray-700">
                                    {{ $item->cpf_cnpj }}
                                </p>
                            </div>

                            {{-- Situação da licença --}}
                            <div class="shrink-0">
                                @if($item->validate_licenca != '')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-1.5"></span>
                                        Ativa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        <span class="w-2 h-2 rounded-full bg-red-500 mr-1.5"></span>
                                        Vencida
                                    </span>
                                @endif
                            </div>

                        </div>

                        {{-- Responsável --}}
                        <div class="mt-5 pt-4 border-t border-gray-100">

                            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">
                                Responsável
                            </p>

                            <p class="text-sm text-gray-700 font-medium mt-1">
                                {{ $item->responsavel->name ?? 'Não informado' }}
                            </p>

                        </div>

                        {{-- Validade --}}
                        <div class="mt-4">

                            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">
                                Validade da licença
                            </p>

                            <p class="text-sm font-medium mt-1
                                {{ $item->validate_licenca != '' ? 'text-green-700' : 'text-red-600' }}">
                                {{ $item->validate_licenca ?: 'Licença vencida ou não cadastrada' }}
                            </p>

                        </div>

                    </div>

                    {{-- Rodapé / ações --}}
                    <div class="bg-gray-50 border-t border-gray-200 px-5 py-3">

                        <div class="flex flex-wrap items-center justify-between gap-2">

                            <a href="{{ route('admin.empresas.edit', $item->id) }}"
                            class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition">

                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>

                                Editar
                            </a>

                            <a href="{{ route('admin.licencas.licenca', $item->id) }}"
                            class="inline-flex items-center px-3 py-2 rounded-lg bg-green-600 text-white text-xs font-semibold hover:bg-green-700 transition">

                                Liberar Licença
                            </a>

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-span-full bg-white rounded-xl border border-gray-200 p-10 text-center">

                    <p class="text-gray-500 text-sm">
                        Nenhuma empresa encontrada.
                    </p>

                </div>

            @endforelse

        </div>
    </div>

    <div class="py-0 mx-auto w-full sm:px-6 lg:px-8">
        {!! $empresas->links('pagination') !!}
    </div>
    <style>
        .top-100 {
            top: 100%
        }

        .bottom-100 {
            bottom: 100%
        }

        .max-h-select {
            max-height: 300px;
        }
    </style>
    @section('jquery')
    @endsection
</x-area-admin-layout>
