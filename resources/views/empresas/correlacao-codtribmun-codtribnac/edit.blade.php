<x-area-cliente-layout title="Edição de Correlação de Cód. Tributação Municipal x Cód. Tributação Nacional">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Edição de Correlação de Cód. Tributação Municipal x Cód. Tributação Nacional') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Edição de Correlação de Cód. Tributação Municipal x Cód. Tributação Nacional
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Gerenciamento de Correlação de Cód. Tributação Municipal x Cód. Tributação Nacional
                </p>
            </h1>
        </div>

        <div class="container grid mx-auto py-10 max-w-10xl">
            {!! Form::model($correlacaoTribMunTribNac,['route'=>['codtrimun-codtribnac.update', [$empresa, $correlacaoTribMunTribNac]]]) !!}
                @method('PUT')
                @include('empresas.correlacao-codtribmun-codtribnac._form')
            {!! Form::close() !!}
        </div>
    </div>
</x-area-cliente-layout>
