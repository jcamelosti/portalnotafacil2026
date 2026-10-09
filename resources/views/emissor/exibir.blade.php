<x-area-empresa-layout title="Notas Emitidas">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Edição de Empresa') }}
        </h2>
    </x-slot>

    <!-- FULL WIDTH -->
    <div class="w-full px-4 py-6 sm:px-6 lg:px-8">

        <div class="bg-white w-full shadow-xl rounded-lg py-2">
            <h1 class="px-4 py-3">
                Dados da NFS-e
            </h1>
        </div>

        <div class="container-list mt-2 w-full">
            <div class="px-4 py-4 mb-8 bg-white rounded-lg shadow-md ">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Número da NFS-e -->
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Nº NFS-e
                        </dt>

                        <dd class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $nota->num_nfse ?? '-' }}
                        </dd>
                    </div>

                    <!-- Valor -->
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Valor
                        </dt>

                        <dd class="mt-1 text-sm font-semibold text-gray-900">
                            R$ {{ number_format($nota->valor, 2, ',', '.') }}
                        </dd>
                    </div>

                    <!-- Empresa -->
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Empresa
                        </dt>

                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $nota->empresa->razao_social ?? '-' }}
                        </dd>
                    </div>

                    <!-- Tomador -->
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Tomador
                        </dt>

                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $nota->tomador->razao_social ?? '-' }}
                        </dd>
                    </div>
                </div>

                <!-- Separador -->
                <div class="border-t border-gray-200 my-6"></div>

                <!-- Informações adicionais -->
                <h3 class="text-sm font-semibold text-gray-800 mb-4">
                    Informações da emissão
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Código de verificação -->
                    <!--div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Código de Verificação
                        </dt>

                        <dd class="mt-1 text-sm text-gray-900 font-mono">
                            {{ $nota->dados_nfse['codigo_verificacao'] ?? '-' }}
                        </dd>
                    </div-->

                    <!-- Série -->
                    <!--div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Série
                        </dt>

                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $nota->dados_nfse['serie'] ?? '-' }}
                        </dd>
                    </div-->

                    <!-- Número RPS -->
                    <!--div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Nº RPS
                        </dt>

                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $nota->dados_nfse['numero_rps'] ?? '-' }}
                        </dd>
                    </div-->

                    <!-- Data de emissão -->
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Data de Emissão
                        </dt>

                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $nota->created_at?->format('d/m/Y H:i:s') ?? '-' }}
                        </dd>
                    </div>

                    @if($nota->cancelada ==! '0')
                    <!-- Data de Cancelamento -->
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Data de Cancelamento
                        </dt>

                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $nota->data_cancelamento->format('d/m/Y') ?? '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Motivo
                        </dt>

                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $nota->motivo_cancelamento ?? '-' }}
                        </dd>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <a target="_blank"
                href="{{ route('nfse.danfse.pdf', $nota->id) }}"
                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-white bg-green-600 rounded hover:bg-green-700">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <path d="M14 2v6h6M8 13h8M8 17h5"/>
                    </svg>

                    Imprimir DANFSe - NFS-e
                </a>
                <a href="{{ route('notas.visualizar-xml', $nota->id) }}"
                target="_blank"
                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-white bg-blue-500 rounded hover:bg-blue-600">

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

                    Exportar XML
                </a>
            </div>
        </div>  
    </div>
</x-area-empresa-layout>
