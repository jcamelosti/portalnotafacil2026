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
                <a href="{{ route('servicos-tomados.index') }}"
                class="inline-flex items-center justify-center gap-2 w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:border-blue-300 transition ease-in-out duration-150 sm:text-sm sm:leading-5">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>

                    Página Anterior
                </a>
            </span>

            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto  ml-4">
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

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                    @foreach($documentos as $doc)
                        <div class="p-5 rounded-xl shadow-sm border border-green-200 bg-green-50 hover:shadow-md transition">

                            {{-- CABEÇALHO --}}
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">
                                        NFS-e: {{ $doc->dados['header']['numero_nfse'] }}
                                    </p>

                                    <h3 class="font-semibold text-gray-800 text-base">
                                        {{ Str::ucfirst($doc->dados['prestador']['nome']) }}
                                    </h3>

                                    <h2>{{ $doc->dados['totais']['liquido_nfse'] }}</h2>
                                </div>
                            </div>

                            {{-- AÇÃO --}}
                            <div class="border-t border-green-200 pt-4">
                                <a href="{{ route('servicos-tomados.visualizar-xml', $doc->id) }}"
                                class="flex items-center justify-center gap-2 w-full px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition">
                                    Baixar XML
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
