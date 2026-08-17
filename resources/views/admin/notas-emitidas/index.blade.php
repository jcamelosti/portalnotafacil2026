<x-area-admin-layout title="Notas Emitidas">
    <div class="py-10 mx-auto w-full sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Notas Fiscais de Serviço
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Listagem de Notas Emitidas
                </p>
            </h1>

            <div class="px-4 py-3 mb-8 bg-white ">
                {!! Form::open(['route' => 'admin.notas.index', 'method' => 'GET']) !!}
                <label class="block text-sm">
                    <span class="text-gray-700 ">Empresa:</span>
                    {!! Form::select('empresa_id', $empresasList, isset($pesquisa['empresa_id']) ? $pesquisa['empresa_id'] : null, ['class' => 'select2  block w-full mt-1 text-sm    form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                    @if ($errors->has('empresa_id'))
                        <span class="text-xs text-red-600 ">
                            <strong>{{ $errors->first('empresa_id') }}</strong>
                        </span>
                    @endif
                </label>
                <div class="bg-gray-50 px-4 py-3 sm:px-8 sm:flex sm:flex-row-reverse">
                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                        <a href="{{ route('admin.notas.index') }}"
                            class="inline-flex justify-center rounded-md border border-blue-700 px-4 mr-2 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            Limpar Filtros
                        </a>
                        <button type="submit" type="button"
                            class="inline-flex justify-center rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            Pesquisar
                        </button>
                    </span>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        <div class="grid gap-4 mt-6">

            @foreach($notas as $nota)
                <div class="p-4 rounded-lg shadow flex flex-col md:flex-row md:items-center md:justify-between
                {{ ($nota->cancelada == '0') ? 'bg-green-50' : 'bg-red-50' }}">

                    <!-- INFORMAÇÕES -->
                    <div class="flex-1">
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

                            <!-- Prestador -->
                            <div>
                                <p class="text-xs text-gray-500">Prestador</p>
                                <p class="text-sm font-semibold">
                                    {{ Str::limit($nota->empresa->razao_social, 25, '...') }}
                                </p>
                            </div>

                            <!-- Tomador -->
                            <div>
                                <p class="text-xs text-gray-500">Tomador</p>
                                <p class="text-sm font-semibold">
                                    {{ Str::limit($nota->tomador->razao_social, 25, '...') }}
                                </p>
                                <!--p class="text-xs text-gray-400">
                                    NFS-e: {{ $nota->id }}
                                </p-->
                            </div>

                            <!-- Valor -->
                            <div>
                                <p class="text-xs text-gray-500">Valor</p>
                                <p class="text-sm font-bold text-green-600">
                                    R$ {{ $nota->valor_nota }}
                                </p>
                            </div>

                            <!-- Data -->
                            <div>
                                <p class="text-xs text-gray-500">Data</p>
                                <p class="text-sm font-semibold">
                                    {{ $nota->data_emissao_nfse }}
                                </p>
                            </div>

                            <!-- Nota -->
                            <div>
                                <p class="text-xs text-gray-500">Nota</p>
                                <p class="text-sm font-semibold">
                                    {{ $nota->num_nfse }}
                                </p>
                            </div>

                            <!-- RPS -->
                            <div>
                                <p class="text-xs text-gray-500">RPS</p>
                                <p class="text-sm font-semibold">
                                    {{ $nota->numero_rps }}
                                </p>
                            </div>

                        </div>
                    </div>

                    <!-- AÇÕES / STATUS -->
                    <div class="mt-4 md:mt-0 flex flex-wrap items-center gap-2 md:justify-end">

                        <!-- PDF -->
                        <a href="{{ $nota->url_view }}"
                        class="px-3 py-1 text-xs font-semibold text-white bg-green-600 rounded hover:bg-green-700">
                            PDF
                        </a>

                        <!-- STATUS -->
                        @if($nota->cancelada == '0')
                            <span class="px-3 py-1 text-xs font-semibold text-white bg-green-700 rounded">
                                Normal
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold text-white bg-red-700 rounded">
                                Cancelada
                            </span>
                        @endif

                    </div>
                </div>
            @endforeach

        </div>
    </div>
    
    <div class="py-0 mx-auto w-full sm:px-6 lg:px-8">
        {{ $notas->render('pagination') }}
    </div>
</x-area-admin-layout>
