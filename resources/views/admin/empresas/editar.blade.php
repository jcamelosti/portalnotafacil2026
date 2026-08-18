<x-area-admin-layout title="Edição de Empresa">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Edição de Empresa') }}
        </h2>
    </x-slot>

    <!-- FULL WIDTH -->
    <div class="w-full px-4 py-6 sm:px-6 lg:px-8">

        <div class="bg-white w-full shadow-xl rounded-lg py-2">
            <h1 class="px-4 py-3">
                Edição de Empresa
                <p class="mt-1 text-sm text-gray-500">
                    Gerenciamento de Empresas
                </p>
            </h1>

            <!-- AÇÕES -->
            <div class="flex flex-wrap gap-2 bg-gray-50 px-4 py-3 mt-4 justify-end">

                <!--a href="{{ route('admin.empresa-cnaes.index', ['empresa-id' => $empresa->id]) }}"
                   class="px-4 py-2 border border-blue-700 bg-white text-sm rounded hover:text-gray-500">
                    CNAES
                </a-->

                <a href="{{ route('admin.empresa-atividades.index', ['empresa-id' => $empresa->id]) }}"
                   class="px-4 py-2 border border-blue-700 bg-white text-sm rounded hover:text-gray-500">
                    Atividades
                </a>

                <a href="{{ route('empresas.sincDataIssNet', $empresa->id) }}"
                   class="px-4 py-2 border border-blue-700 bg-white text-sm rounded hover:text-gray-500">
                    Sinc. Dados Cadastrais
                </a>

                <!--a href="{{ route('admin.empresas.dados-receita', $empresa->id) }}"
                   class="px-4 py-2 border border-blue-700 bg-white text-sm rounded hover:text-gray-500">
                    Sinc. Receita Federal
                </a-->
            </div>
        </div>

        <!-- FORM FULL -->
        <div class="w-full py-6">
            {!! Form::model($empresa,[
                'route'=>['admin.empresas.update', $empresa->id]
            ]) !!}
                @method('PUT')
                @include('admin.empresas._form')
            {!! Form::close() !!}
        </div>

        <!-- BOTÃO PERIGO -->
        <div class="w-full mt-4">
            <a href="{{ route('admin.empresas.remover-dados', $empresa->id) }}"
               class="inline-block px-4 py-2 border border-red-600 text-red-600 rounded hover:bg-red-600 hover:text-white transition">
                Excluir Todas as Informações desta Empresa
            </a>
        </div>

    </div>
</x-area-admin-layout>