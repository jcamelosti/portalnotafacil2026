<x-area-empresa-layout title="Cancelamento de Nota">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Cancelamento de Nota') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Nota Fiscal de Serviços
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Cancelamento de Nota fiscal de serviço
                </p>
            </h1>
        </div>

        <div class="container grid mx-auto w-full py-10 max-w-10xl">
            <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md ">
                @include('components.mensagens')
                {!! Form::model($nota, ['route' => ['notas.cancelar-issnet', $nota->id]]) !!}
                <label class="block text-sm">
                    <span class="text-gray-700 ">Código Interno da Nota Fiscal:</span>
                    {!! Form::text('id', null, ['disabled', 'class'=>'disabled:opacity-50 block w-full mt-1 text-sm
                      focus:border-purple-400 focus:outline-none focus:shadow-outline-purple
                     :shadow-outline-gray form-input', 'placeholder'=>'']) !!}
                </label>
                <label class="block text-sm">
                    <span class="text-red-700 ">Número do RPS a ser Cancelado:</span>
                    {!! Form::text('num_nfse', null, ['disabled', 'class'=>'disabled:opacity-50 block w-full mt-1 text-sm
                      focus:border-purple-400 focus:outline-none focus:shadow-outline-purple
                     :shadow-outline-gray form-input', 'placeholder'=>'']) !!}
                </label>
                <label class="block text-sm">
                    <span class="text-red-700 ">Razão Tomador:</span>
                    {!! Form::text('tomador', $nota->tomador->razao_social, ['disabled', 'class'=>'disabled:opacity-50 block w-full mt-1 text-sm
                      focus:border-purple-400 focus:outline-none focus:shadow-outline-purple
                     :shadow-outline-gray form-input', 'placeholder'=>'']) !!}
                </label>
                <label class="block text-sm">
                    <span class="text-red-700 ">CPF/CNPJ Tomador:</span>
                    {!! Form::text('tomadorcnpj', $nota->tomador->cpf_cnpj, ['disabled', 'class'=>'disabled:opacity-50 block w-full mt-1 text-sm
                      focus:border-purple-400 focus:outline-none focus:shadow-outline-purple
                     :shadow-outline-gray form-input', 'placeholder'=>'']) !!}
                </label>
                <label class="block text-sm">
                    <span class="text-red-700 ">Motivo do Cancelamento:</span>
                    {!! Form::select('motivo', $motivo, 2, ['required','class'=>'block w-full mt-1 text-sm  
                    
                    form-select
                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                    @if ($errors->has('motivo'))
                        <span class="text-xs text-red-600 ">
                        <strong>{{ $errors->first('motivo') }}</strong>
                    </span>
                    @endif
                </label>
                <span class="text-red-700 ">Justificativa Cancelamento:</span>
                    {!!
                        Form::textarea('justificativa', null, [
                            'name'=>"justificativa",
                            'id'=>"justificativa",
                            'style'=>"height: 100px !important;",                        
                            'maxlength'=>"2000",
                            'required',
                            'class'=>'block w-full mt-1 text-sm  
                            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                            :shadow-outline-gray form-input',
                            'placeholder'=>'Motivo Cancelamento',
                            'id' => 'justificativa'
                        ]) !!}

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                        <a href="{{ route('nota.index') }}"
                        class="inline-flex justify-center w-full rounded-md border border-green-300 px-4 py-2 bg-green-600 text-base leading-6 text-white font-medium shadow-sm hover:text-white-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            Não vou Cancelar a Nota
                        </a>
                    </span>
                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                        <button type="submit" type="button"
                                class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-red-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            Cancelar Nota Agora
                        </button>
                    </span>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</x-area-empresa-layout>
