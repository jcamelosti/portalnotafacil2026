<x-area-empresa-layout title="Notas Emitidas">
    <div class="py-10 mx-auto sm:px-6 lg:px-8">
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
                <a href=""
                   class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                    Sincronizar Notas de Serviços Tomados
                </a>
            </span>
        </div>

        <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                @include('components.mensagens')

                <form method="GET" action="{{ route('servicos-tomados.index') }}"
                    class="bg-white p-4 rounded-lg shadow mb-6">

                    <div class="grid grid-cols-1 gap-4">

                        <!-- Tomador (linha inteira) -->
                        <label class="block text-sm">
                            <span class="text-gray-700">Emitente:</span>
                            <!--{ ! ! Form::select(
                                'tomador_id',
                                $tomadoresList,
                                request()->tomador_id ?? null,
                                ['class'=>'select2 block w-full mt-1 text-sm form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple']
                            ) ! ! } -->
                        </label>

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

                <div class="grid gap-4 mt-6">
                    @foreach($documentos as $doc)
                        <div class="p-4 rounded-lg shadow flex flex-col md:flex-row md:items-center md:justify-between
                        {{ ($doc->id > '0') ? 'bg-green-50' : 'bg-red-50' }}">

                            <!-- DADOS -->
                            <div class="flex-1">
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                                    <div>
                                        <p class="text-xs text-gray-500">CNPJ</p>
                                        <p class="font-semibold text-sm">
                                            {{ $doc->dados['prestador']['cnpj'] }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-500">Razão Social</p>
                                        <p class="font-semibold text-sm">
                                            {{ Str::limit($doc->dados['prestador']['nome'] , 25, '...') }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-500">Emissão em</p>
                                        <p class="font-semibold text-sm">
                                           {{ $doc->dados['header']['emissao_nfse'] }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-500">Núm. NFSE</p>
                                        <p class="font-semibold text-sm">
                                             {{ $doc->dados['header']['numero_nfse'] }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-500">Valor</p>
                                        <p class="font-bold text-green-600 text-sm">
                                           {{ $doc->dados['totais']['operacao']  }}
                                        </p>
                                    </div>

                                </div>
                            </div>

                            <!-- AÇÕES -->
                            <div class="mt-4 md:mt-0 flex flex-wrap gap-2 md:justify-end">
                                <a href="{{ route('servicos-tomados.visualizar-xml', $doc->id) }}"
                                class="px-3 py-1 text-xs font-semibold text-white bg-green-500 rounded hover:bg-green-600">
                                    XML
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
</x-area-empresa-layout>
