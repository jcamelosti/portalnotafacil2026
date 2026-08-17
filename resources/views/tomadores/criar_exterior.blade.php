<x-area-empresa-layout title="Adicionar Tomador">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Adicionar Tomador') }}
        </h2>
    </x-slot>

    <!-- FULL WIDTH -->
    <div class="w-full px-4 py-6 sm:px-6 lg:px-8">

        <div class="bg-white w-full shadow-xl rounded-lg py-2">
            <h1 class="px-4 py-3">
                Adicionar Tomador (Exterior)
                <p class="mt-1 text-sm text-gray-500">
                    Gerenciamento de Tomadores
                </p>
            </h1>
        </div>

        <!-- FORM FULL -->
        <div class="w-full py-6">
            {!! Form::open(['route'=>'tomadores.store_ext', 'name'=> 'Form1']) !!}
                @include('tomadores._form_exterior')
            {!! Form::close() !!}
        </div>

    </div>
</x-area-empresa-layout>