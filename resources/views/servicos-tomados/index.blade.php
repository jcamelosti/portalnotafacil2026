<x-area-cliente-layout title="Notas Tomadas">
    <div class="mx-auto sm:px-6 lg:px-8 py-2">
        @include('components.empresa-selecionada')

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Notas Fiscais de Serviço Tomadas
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Listagem de Notas Tomadas
                </p>
            </h1>
        </div>

        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row">
            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                <a href="{{ route('servicos-tomados.sincronizar') }}"
                   class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                    Sincronizar Notas de Serviços Tomados
                </a>
            </span>
            
            <span class="ml-4 mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                <a href="{{ route('servicos-tomados.selecionar-empresa') }}"
                    class="inline-flex justify-center w-full rounded-md border border-blue-600 px-4 py-2 bg-white text-base leading-6 font-medium text-blue-700 shadow-sm hover:text-blue-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                    Trocar de Empresas
                </a>
            </span>
        </div>

        <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                @include('components.mensagens')

                <form method="GET" action="{{ route('servicos-tomados.index') }}"
                    class="bg-white p-4 rounded-lg shadow mb-6">

                    <div class="grid grid-cols-1 gap-4">
                        <!-- Linha de baixo -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <!-- Data Inicial -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Data Inicial
                                </label>
                                <input type="date" name="data_inicio"
                                    value="{{ request('data_inicio') ?? $data['data_inicio'] ?? null }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Data Final -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Data Final
                                </label>
                                <input type="date" name="data_fim"
                                    value="{{ request('data_fim') ?? $data['data_fim'] ?? null }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Botões -->
                            <div class="md:col-span-2 flex gap-2">
                                <button type="submit"
                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition">
                                    🔍 Filtrar
                                </button>

                                <a href="{{ route('nota.index') }}"
                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md text-center transition">
                                    Limpar
                                </a>
                            </div>

                        </div>

                    </div>

                </form>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                    @foreach($documentos as $doc)
                        <div class="p-5 rounded-xl shadow-sm border border-green-200 bg-green-50 hover:shadow-md transition">

                            {{-- CABEÇALHO --}}
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">
                                        Período
                                    </p>

                                    <h3 class="font-semibold text-gray-800 text-base">
                                        {{ Str::ucfirst($doc->mes_ano) }}
                                    </h3>
                                </div>

                                <div class="bg-green-100 text-green-700 rounded-full p-3">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="w-6 h-6"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round">

                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <path d="M14 2v6h6"/>
                                        <path d="m10 12-2 2 2 2"/>
                                        <path d="m14 12 2 2-2 2"/>
                                    </svg>
                                </div>
                            </div>

                            {{-- TOTAL --}}
                            <div class="mb-5">
                                <p class="text-xs text-gray-500 mb-1">
                                    Total de NFS-e
                                </p>

                                <p class="text-3xl font-bold text-green-600">
                                    {{ $doc->total }}
                                </p>
                            </div>

                            {{-- AÇÃO --}}
                            <div class="border-t border-green-200 pt-4">
                                <a href="{{ route('servicos-tomados.visualizar-xml-periodo', $doc->periodo) }}"
                                class="flex items-center justify-center gap-2 w-full px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition">
                                    Baixar XMLs
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    {{ $documentos->render('pagination') }}
    </div>

@section('jquery')
<script>
    /*setInterval(function() {
        location.reload();
    }, 10000);*/
</script>
@endsection
</x-area-cliente-layout>
