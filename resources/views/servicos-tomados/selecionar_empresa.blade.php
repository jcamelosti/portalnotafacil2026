<x-app-layout title="Consulta de Notas Tomadas">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Consulta de Notas') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        @include('components.mensagens')
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Consulta Notas Tomadas
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    
                </p>
            </h1>

            <div class="px-4 py-3 mb-8 bg-white ">
                {!! Form::open(['route'=>'servicos-tomados.selecionar-empresa', 'method'=>'POST']) !!}
                {!! Form::select(
                        'empresa_id',
                        $empresasList,
                        isset($pesquisa['empresa_id']) ? $pesquisa['empresa_id'] : null,
                        [
                            'required',
                            'class' => 'select2 block w-full mt-1 text-sm form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple shadow-outline-gray'
                        ]
                    ) !!}
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                        <button type="submit" type="button"
                                class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                        Selecionar
                      </button>
                    </span>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        
    </div>
</x-app-layout>
