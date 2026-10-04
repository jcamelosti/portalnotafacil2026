<x-area-cliente-layout title="Edição de Empresa">
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
                <a href="{{ route('codtrimun-codtribnac.index', $empresa) }}"
                   class="inline-flex justify-center rounded-md border border-blue-700 px-4 py-2 bg-white text-sm font-medium text-gray-700 shadow-sm hover:text-gray-500">
                    Correlação Cód Trib. Municipal x Cód Trib. Nacional
                </a>
                <a href="{{ route('empresa-nbs.index', $empresa) }}"
                   class="inline-flex justify-center rounded-md border border-blue-700 px-4 py-2 bg-white text-sm font-medium text-gray-700 shadow-sm hover:text-gray-500">
                    Cadastro de NBS
                </a>
                <a href="{{ route('empresas.sincDataIssNet', $empresa->id) }}"
                   class="inline-flex justify-center rounded-md border border-blue-700 px-4 py-2 bg-white text-sm font-medium text-gray-700 shadow-sm hover:text-gray-500">
                    Sinc. Dados Cadastrais ISSNet
                </a>
            </div>
        </div>

        <!-- FORM FULL -->
        <div class="w-full py-6">
            {!! Form::model($empresa,[
                'route'=>['empresas.update', $empresa->id],
                'name'=> 'Form1'
            ]) !!}
                @method('PUT')
                @include('empresas._form')
            {!! Form::close() !!}
        </div>

    </div>
</x-area-cliente-layout>