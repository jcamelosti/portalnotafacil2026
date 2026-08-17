<x-area-empresa-layout title="Dashboard">

    <!-- FULL WIDTH -->
    <div class="w-full px-4 py-6">

        <h2 class="mb-6 text-2xl font-semibold text-gray-700">
            Painel de Administração de Empresa
        </h2>

        @include('components.mensagens')

        <div class="mb-6">
            <h2 class="text-lg font-semibold">
                {{ $empresa->razao_social }} - {{ $empresa->cpf_cnpj_fmt }}
            </h2>
        </div>

        <!-- CARDS -->
        <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">

            <div class="p-4 bg-white rounded-lg shadow">
                <p class="text-sm font-medium text-green-600">Notas Emitidas Mês</p>
                <p class="text-lg font-semibold text-green-700">{{ $notasEmitidas }}</p>
            </div>

            <div class="p-4 bg-white rounded-lg shadow">
                <p class="text-sm font-medium text-red-600">Notas Canceladas no Mês</p>
                <p class="text-lg font-semibold text-red-700">{{ $notasCanceladas }}</p>
            </div>

            <div class="p-4 bg-white rounded-lg shadow">
                <p class="text-sm font-medium text-blue-600">Total de Notas Emitidas</p>
                <p class="text-lg font-semibold text-blue-700">{{ $notasEmitidasTotal }}</p>
            </div>

            <div class="p-4 bg-white rounded-lg shadow">
                <p class="text-sm font-medium text-yellow-600">Total de Notas Canceladas</p>
                <p class="text-lg font-semibold text-yellow-700">{{ $notasCanceladasTotal }}</p>
            </div>

        </div>

        <!-- LISTA -->
        <h2 class="mb-4 text-lg font-semibold">Últimas Notas Emitidas</h2>

        <div class="w-full mb-8">
            <div class="grid gap-4">

                @foreach($notas as $nota)
                    <div class="p-4 rounded-lg shadow flex flex-col md:flex-row md:items-center md:justify-between 
                        {{ ($nota->cancelada == '0') ? 'bg-green-50' : 'bg-red-50' }}">

                        <!-- INFO -->
                        <div class="flex-1">
                            <div class="flex flex-col md:flex-row md:gap-6">

                                <div>
                                    <p class="text-sm text-gray-500">CPF/CNPJ</p>
                                    <p class="font-semibold">{{ $nota->tomador->cpf_cnpj_fmt }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Tomador</p>
                                    <p class="font-semibold">
                                        {{ Str::limit($nota->tomador->razao_social, 30, '...') }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Data</p>
                                    <p class="font-semibold">{{ $nota->data_emissao_nfse }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">RPS</p>
                                    <p class="font-semibold">{{ $nota->numero_rps }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Valor</p>
                                    <p class="font-semibold text-green-600">
                                        R$ {{ $nota->valor_nota }}
                                    </p>
                                </div>

                            </div>
                        </div>

                        <!-- AÇÕES -->
                        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">

                            <a target="_blank"
                               href="{{ route('notas.visualizacao-publica', [base64_encode(strrev(substr($nota->tomador->cpf_cnpj,0,5))), base64_encode($nota->id)]) }}"
                               class="px-3 py-1 text-sm text-white bg-green-600 rounded hover:bg-green-700">
                                PDF
                            </a>

                            <a href="{{ route('notas.visualizar-xml', $nota->id) }}"
                               class="px-3 py-1 text-sm text-white bg-green-500 rounded hover:bg-green-600">
                                XML
                            </a>

                            @if($nota->cancelada == '0' && $nota->can_cancel)
                                <a href="{{ route('notas.cancelar-issnet', $nota->id) }}"
                                   class="px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700">
                                    Cancelar
                                </a>
                            @endif

                            <a href="{{ route('notas.substitucao', $nota->id) }}"
                               class="px-3 py-1 text-sm text-white bg-orange-600 rounded hover:bg-orange-700">
                                Substituir
                            </a>

                            @if(!empty($nota->dados_emissao_json))
                                <a href="{{ route('notas.duplicar', base64_encode($nota->id)) }}"
                                   class="px-3 py-1 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                                    Duplicar
                                </a>
                            @endif

                        </div>
                    </div>
                @endforeach

            </div>
        </div>

    </div>

</x-area-empresa-layout>