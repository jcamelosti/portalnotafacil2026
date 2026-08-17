<x-area-admin-layout title="Edição de Atividades">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Edição de Atividades') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Edição de Atividades
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Gerenciamento de Atividades
                </p>
            </h1>
        </div>

        <div class="container grid mx-auto py-10 max-w-10xl">
            {!! Form::model($atividade,['route'=>['admin.empresa-atividades.update', $atividade->id]]) !!}
                @method('PUT')
                @include('admin.empresa-atividades._form')
            {!! Form::close() !!}
        </div>
    </div>
</x-area-admin-layout>
