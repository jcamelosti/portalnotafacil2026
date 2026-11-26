<x-area-admin-layout title="Vencimento de Licenças">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Vencimento de Licenças') }}
        </h2>
    </x-slot>

    <div class="w-full px-4 py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-lg py-1">
            <h1 class="px-4 py-3">
                Licenças Vencidas / A Vencer
                <p class="mt-1 text-sm text-gray-500">
                    Empresas com licença vencida ou que vencem nos próximos 15 dias
                </p>
            </h1>
            <div class="flex justify-end bg-gray-50 px-4 py-3">
                <a href="{{ route('admin.licencas.index') }}"
                   class="px-4 py-2 border border-blue-700 bg-white text-sm rounded hover:text-gray-500">
                    Gestão de Licenças
                </a>
            </div>
        </div>

        @include('components.mensagens')

        @include('admin.licencas._tabela-vencimentos', [
            'titulo' => 'Licenças Vencidas',
            'registros' => $licVenc,
            'linkRemoverDados' => true,
        ])

        @include('admin.licencas._tabela-vencimentos', [
            'titulo' => 'Licenças a Vencer',
            'registros' => $licAVenc,
            'linkRemoverDados' => false,
        ])
    </div>
</x-area-admin-layout>
