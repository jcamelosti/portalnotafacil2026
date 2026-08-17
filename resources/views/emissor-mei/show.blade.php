<x-area-empresa-layout title="Visualizar Serviço">

<div class="py-6 w-full px-4 space-y-4">

    {{-- HEADER --}}
    <div class="bg-white shadow rounded-lg p-4 flex justify-between items-center">
        <div>
            <h1 class="text-lg font-semibold">
                Visualização do Serviço
            </h1>

            <p class="text-sm text-gray-500">
                Detalhes do serviço prestado
            </p>
        </div>

        <a href="{{ route('servicos-mei.index') }}"
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
           Voltar
        </a>
    </div>

    {{-- DADOS --}}
    <div class="bg-white shadow rounded-lg p-6 space-y-6">

        {{-- TOMADOR --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-700 mb-2">
                DADOS DO TOMADOR DO SERVIÇO
            </h2>
            
            @if(!is_null($servico->tomador_id))
            <div class="border rounded-lg p-3 text-sm">
                <strong>{{ $servico->tomador->cpf_cnpj_fmt }}</strong> - 
                {{ $servico->tomador->razao_social }}
            </div>
            @else
            <div class="border rounded-lg p-3 text-sm">
                <strong>{{ $servico->empresa_cliente->cpf_cnpj_fmt }}</strong> - 
                {{ $servico->empresa_cliente->razao_social }}
            </div>
            @endif
        </div>

        {{-- LOCAL --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-700 mb-2">
                LOCAL DA PRESTAÇÃO DO SERVIÇO
            </h2>

            <div class="border rounded-lg p-3 text-sm">
                {{ $servico->municipio->municipio ?? 'Não informado' }}
            </div>
        </div>

        {{-- SERVIÇO --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-700 mb-2">
                SERVIÇO PRESTADO
            </h2>

            <div class="space-y-3 text-sm">

                <div>
                    <span class="text-gray-500">Código:</span><br>
                    <strong>{{ $servico->codigoTributacao->descricao ?? '-' }}</strong>
                </div>

                <div>
                    <span class="text-gray-500">Descrição:</span>
                    <div class="border rounded-lg p-3 mt-1">
                        {!! nl2br(e($servico->descricao)) !!}
                    </div>
                </div>

                <div>
                    <span class="text-gray-500">NBS:</span><br>
                    <strong>{{ $servico->nbs->codigo_nbs . ' - '.$servico->nbs->descricao_nbs ?? '-' }}</strong>
                </div>

            </div>
        </div>

        {{-- VALORES --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-700 mb-2">
                VALORES DO SERVIÇO PRESTADO
            </h2>

            <div class="border rounded-lg p-3 text-sm">
                <span class="text-gray-500">Valor:</span><br>
                <strong class="text-green-600 text-lg">
                    R$ {{ $servico->valor_servico_fmt }}
                </strong>
            </div>
        </div>

        {{-- AÇÕES --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-end">

            {{-- TRANSMITIR --}}
           @if (
                empty($servico->chave_nfse) ||
                (
                    !empty($servico->fatura) && !is_null($servico->fatura_fatura_status_id) &&
                    $servico->fatura->fatura_status_id == 1
                )
            )
                <form action="{{ route('servicos-mei.transmitir', $servico->id) }}" method="POST">
                    @csrf

                    <button
                        type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm font-semibold w-full sm:w-auto">
                        🚀 Transmitir NFS-e
                    </button>
                </form>
            @else
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg text-sm font-semibold text-center">
                    ✔️ NFS-e já transmitida
                </div>
            @endif

            {{-- BAIXAR DANFE --}}
            @if (!empty($servico->chave_nfse))
                <a href="{{ route('servicos-mei.danfe', $servico->id) }}"
                target="_blank"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold text-center w-full sm:w-auto">
                📄 Baixar DANFSE
                </a>
            @endif

        </div>

        {{-- DATAS --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-700 mb-2">
                INFORMAÇÕES ADICIONAIS
            </h2>

            <div class="grid grid-cols-2 gap-4 text-sm">

                <div>
                    <span class="text-gray-500">Criado em:</span><br>
                    {{ $servico->created_at?->format('d/m/Y H:i') }}
                </div>

                <div>
                    <span class="text-gray-500">Atualizado em:</span><br>
                    {{ $servico->updated_at?->format('d/m/Y H:i') }}
                </div>

            </div>
        </div>

    </div>

</div>

</x-area-empresa-layout>