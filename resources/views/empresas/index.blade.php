
<x-area-cliente-layout title="Empresas">

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Empresas
                </h2>
                <p class="text-sm text-gray-500">
                    Gerencie suas empresas e licenças.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 mx-auto w-full sm:px-6 lg:px-8">

        {{-- Cabeçalho e ações --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="p-5">

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                    <div>
                        <h1 class="text-lg font-bold text-gray-800">
                            Minhas Empresas
                        </h1>

                        <p class="text-sm text-gray-500 mt-1">
                            Consulte e gerencie as empresas vinculadas à sua conta.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row flex-wrap gap-2">

                        <a href="{{ route('empresas.listar-solicitacao-acesso-empresa') }}"
                           class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">

                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>

                            Compartilhamento
                        </a>

                        <a href="{{ route('empresas.adicionar') }}"
                           class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">

                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-4.35-4.35M11 19a8 8 0 110-16 8 8 0 010 16z"/>
                            </svg>

                            Adicionar por CNPJ
                        </a>

                        <a href="{{ route('empresas.create') }}"
                           class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition">

                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4v16m8-8H4"/>
                            </svg>

                            Nova Empresa
                        </a>

                    </div>

                </div>

            </div>

            {{-- Pesquisa --}}
            <div class="bg-gray-50 border-t border-gray-200 px-5 py-4">

                {!! Form::open(['route' => 'empresas.index', 'method' => 'GET']) !!}

                <div class="flex flex-col sm:flex-row sm:items-end gap-3">

                    <div class="flex-1">
                        <label for="empresa_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Pesquisar empresa
                        </label>

                        {!! Form::select(
                            'empresa_id',
                            $empresasList,
                            isset($pesquisa['empresa_id']) ? $pesquisa['empresa_id'] : null,
                            [
                                'id' => 'empresa_id',
                                'class' => 'select2 block w-full mt-1 text-sm form-select focus:border-blue-500 focus:ring-blue-500 rounded-lg border-gray-300',
                                'placeholder' => 'Todas as empresas'
                            ]
                        ) !!}

                        @if ($errors->has('empresa_id'))
                            <span class="text-xs text-red-600">
                                {{ $errors->first('empresa_id') }}
                            </span>
                        @endif
                    </div>

                    <div class="flex gap-2">

                        <button type="submit"
                                class="mt-2 inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition">

                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-4.35-4.35M11 19a8 8 0 110-16 8 8 0 010 16z"/>
                            </svg>

                            Pesquisar
                        </button>

                        <a href="{{ route('empresas.index') }}"
                           class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition">

                            Limpar
                        </a>

                    </div>

                </div>

                {!! Form::close() !!}

            </div>

        </div>

        {{-- Mensagens --}}
        <div class="mt-4">
            @include('components.mensagens')
        </div>

        {{-- Legenda --}}
        <div class="flex flex-wrap items-center gap-4 mt-6 mb-4 text-xs text-gray-600">

            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                Empresa integrada
            </div>

            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-gray-400"></span>
                Empresa não integrada
            </div>

            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                Licença válida
            </div>

            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                Licença vencida
            </div>

        </div>

        {{-- Listagem em cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

            @forelse ($empresas as $item)

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition duration-200 overflow-hidden
                    {{ $item->id_integracao != '' ? 'border-l-4 border-l-green-500' : 'border-l-4 border-l-gray-300' }}">

                    {{-- Conteúdo --}}
                    <div class="p-5">

                        {{-- Nome e situação --}}
                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0 flex-1">

                                <h3 class="text-base font-bold text-gray-800 leading-snug break-words">
                                    {{ $item->razao_social }}
                                </h3>

                                <p class="text-xs text-gray-500 mt-2 uppercase font-semibold">
                                    CNPJ / CPF
                                </p>

                                <p class="text-sm font-semibold text-gray-700 mt-1">
                                    {{ $item->cpf_cnpj }}
                                </p>

                            </div>

                            {{-- Integração --}}
                            @if($item->id_integracao != '')
                                <span class="shrink-0 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    Integrada
                                </span>
                            @else
                                <span class="shrink-0 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    Não integrada
                                </span>
                            @endif

                        </div>

                        {{-- Informações da licença --}}
                        <div class="mt-5 pt-4 border-t border-gray-100">

                            <div class="flex items-center justify-between gap-2">

                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">
                                        Licença
                                    </p>

                                    @if($item->validate_licenca != '')
                                        <p class="text-sm font-semibold text-green-700 mt-1">
                                            Válida até {{ $item->validate_licenca }}
                                        </p>
                                    @else
                                        <p class="text-sm font-semibold text-red-600 mt-1">
                                            Vencida ou não cadastrada
                                        </p>
                                    @endif
                                </div>

                                @if($item->validate_licenca != '')
                                    <span class="w-9 h-9 flex items-center justify-center rounded-full bg-green-100 text-green-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                @else
                                    <span class="w-9 h-9 flex items-center justify-center rounded-full bg-red-100 text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </span>
                                @endif

                            </div>

                        </div>

                    </div>

                    {{-- Ações --}}
                    <div class="bg-gray-50 border-t border-gray-200 px-5 py-3">

                        <div class="flex flex-wrap items-center gap-2">

                            <a href="{{ route('empresas.edit', $item->id) }}"
                               class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-lg bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition">

                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>

                                Editar
                            </a>

                            @if($item->user_id == $user_id)

                                <a href="{{ route('licenca.renovacao', ['id' => $item->id]) }}"
                                   class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-lg text-white text-xs font-semibold transition
                                   {{ $item->validate_licenca != '' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}">

                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 4v5h5M20 20v-5h-5M5.64 5.64A9 9 0 0119 12m-14 0a9 9 0 0013.36 6.36"/>
                                    </svg>

                                    Renovar Licença
                                </a>

                            @endif

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-span-full bg-white rounded-xl border border-gray-200 p-10 text-center">

                    <div class="flex justify-center mb-4">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/>
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-base font-semibold text-gray-800">
                        Nenhuma empresa encontrada
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Você ainda não possui empresas cadastradas ou a pesquisa não retornou resultados.
                    </p>

                    <a href="{{ route('empresas.create') }}"
                       class="inline-flex items-center mt-5 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">

                        Adicionar Empresa
                    </a>

                </div>

            @endforelse

        </div>

        {{-- Paginação --}}
        <div class="mt-8">
            {!! $empresas->links('pagination') !!}
        </div>

    </div>

    @section('jquery')
    @endsection

</x-area-cliente-layout>
