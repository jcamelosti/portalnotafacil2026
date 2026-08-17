<x-area-empresa-layout title="Edição de Tomador">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Edição de Tomador') }}
        </h2>
    </x-slot>

    <!-- FULL WIDTH -->
    <div class="w-full px-4 py-6 sm:px-6 lg:px-8">

        <div class="bg-white w-full shadow-xl rounded-lg py-2">
            <h1 class="px-4 py-3">
                Edição de Tomador - {{$tomador->cidade_id == "99999" ? "Residente no Exterior": ""}}
                <p class="mt-1 text-sm text-gray-500">
                    Gerenciamento de Tomadores
                </p>
            </h1>
        </div>

        <!-- FORM FULL -->
        <div class="w-full py-6">

            @if($tomador->cidade_id != "99999")
                {!! Form::model($tomador,[
                    'route'=>['tomadores.update', $tomador->id],
                    'name'=> 'Form1'
                ]) !!}
                    @method('PUT')
                    @include('tomadores._form')
                {!! Form::close() !!}
            @else
                {!! Form::model($tomador,[
                    'route'=>['tomadores.update_ext', $tomador->id],
                    'name'=> 'Form1'
                ]) !!}
                    @method('PUT')
                    @include('tomadores._form_exterior')
                {!! Form::close() !!}
            @endif

        </div>

    </div>
</x-area-empresa-layout>