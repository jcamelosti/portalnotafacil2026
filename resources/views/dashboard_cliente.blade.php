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

        
    </div>
</x-area-empresa-layout>