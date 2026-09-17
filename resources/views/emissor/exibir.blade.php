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

                    <!-- Data de emissão -->
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Data de Cancelamento
                        </dt>

                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $nota->data_cancelamento?->format('d/m/Y H:i:s') ?? '-' }}
                        </dd>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</x-area-empresa-layout>
