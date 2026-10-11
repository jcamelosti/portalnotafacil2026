<x-area-empresa-layout title="Dashboard">

    <!-- FULL WIDTH -->
    <div class="w-full px-4 py-6">

        <h2 class="mb-6 text-2xl font-semibold text-gray-700">
            Painel de Administração de Empresa
        </h2>

        @include('components.mensagens')

        <!--div class="mb-6">
            <h2 class="text-lg font-semibold">
                {{ $empresa->razao_social }} - {{ $empresa->cpf_cnpj_fmt }}
            </h2>
        </div-->

        <!-- LISTA -->
        <h2 class="mb-4 text-lg font-semibold">Últimas Notas Emitidas</h2>

        <div class="grid gap-4 mt-6">
                        @foreach($notas as $nota)
                            <div class="p-4 rounded-lg shadow flex flex-col md:flex-row md:items-center md:justify-between
                            {{ ($nota->cancelada == '0') ? 'bg-green-50' : 'bg-red-50' }}">

                                <!-- DADOS -->
                                <div class="flex-1">
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                                        <div>
                                            <p class="text-xs text-gray-500">CPF/CNPJ</p>
                                            <p class="font-semibold text-sm">
                                                {{ !empty($nota->tomador->cpf_cnpj) ? $nota->tomador->cpf_cnpj_fmt : ' - '}}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-gray-500">Tomador</p>
                                            <p class="font-semibold text-sm">
                                                {{ Str::limit($nota->tomador->razao_social, 25, '...') }}
                                            </p>
                                            <!--p class="text-xs text-gray-400">NFS-e: {{ $nota->id }}</p-->
                                        </div>

                                        <div>
                                            <p class="text-xs text-gray-500">Data</p>
                                            <p class="font-semibold text-sm">
                                                {{ $nota->created_at->format('d/m/Y G:i:s') }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-gray-500">Núm. NFSE</p>
                                            <p class="font-semibold text-sm">
                                                {{ $nota->num_nfse }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs text-gray-500">Valor</p>
                                            <p class="font-bold text-green-600 text-sm">
                                                R$ {{ number_format($nota->valor, 2, ',', '.')}}
                                            </p>
                                        </div>

                                    </div>
                                </div>

                                <!-- AÇÕES -->
                                <div class="mt-4 md:mt-0 flex flex-wrap gap-2 md:justify-end">
                                    <a href="{{ route('notas.show', $nota->id) }}"
                                    class="px-3 py-1 text-xs font-semibold text-white bg-teal-500 rounded hover:bg-teal-600">
                                        Visualizar
                                    </a>

                                    <!--a target="_blank"
                                    href="{{ route('notas.visualizacao-publica', [base64_encode(strrev(substr($nota->tomador->cpf_cnpj,0,5))), base64_encode($nota->id)]) }}"
                                    class="px-3 py-1 text-xs font-semibold text-white bg-green-600 rounded hover:bg-green-700">
                                        PDF
                                    </a-->

                                    <a target="_blank"
                                    href="{{ route('nfse.danfse.pdf', $nota->id) }}"
                                    class="px-3 py-1 text-xs font-semibold text-white bg-green-600 rounded hover:bg-green-700">
                                        PDF
                                    </a>

                                    <a href="{{ route('notas.visualizar-xml', $nota->id) }}"
                                    class="px-3 py-1 text-xs font-semibold text-white bg-green-500 rounded hover:bg-green-600">
                                        XML
                                    </a>

                                    @if($nota->cancelada == '0')
                                        @if($nota->can_cancel)
                                            <!--a href="{{ route('notas.cancelar-issnet', $nota->id) }}"
                                            class="px-3 py-1 text-xs font-semibold text-white bg-red-600 rounded hover:bg-red-700">
                                                Cancelar
                                            </a-->
                                        @else
                                            <!--span class="px-3 py-1 text-xs font-semibold text-gray-500 bg-gray-200 rounded">
                                                Indisponível
                                            </span-->
                                        @endif
                                    @else
                                        <span class="px-3 py-1 text-xs font-semibold text-white bg-red-800 rounded">
                                            Cancelada
                                        </span>
                                    @endif

                                    @if($nota->cancelada == '0')
                                        <!--a href="{{ route('notas.substitucao', $nota->id) }}"
                                        class="px-3 py-1 text-xs font-semibold text-white bg-orange-600 rounded hover:bg-orange-700">
                                            Substituir
                                        </a-->
                                    @endif

                                    @if(!empty($nota->dados_emissao))
                                    <a href="{{ route('notas.duplicar', base64_encode($nota->id)) }}"
                                        class="px-3 py-1 text-xs font-semibold text-white bg-blue-600 rounded hover:bg-blue-700">
                                            Duplicar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-area-empresa-layout>