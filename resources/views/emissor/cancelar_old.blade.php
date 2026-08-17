<x-area-empresa-layout title="Notas Emitidas">
    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Notas Fiscais de Serviço
                <p class="mt-1 max-w-2xl text-sm text-red-500">
                   Cancelamento de Notas Emitidas
                </p>
            </h1>
        </div>

        <div class="container grid px-6 pt-4 mx-auto">
            <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs">
                <div class="w-full overflow-x-auto">
                    @include('components.mensagens')
                    {!! Form::model($nota, ['route' => ['notas.cancelar-issnet', $nota->id]]) !!}
                    <label class="block text-sm">
                        <span class="text-red-700 ">Número do RPS a ser Cancelado:</span>
                        {!! Form::text('num_nfse', null, ['disabled', 'class'=>'disabled:opacity-50 block w-full mt-1 text-sm
                          focus:border-purple-400 focus:outline-none focus:shadow-outline-purple
                         :shadow-outline-gray form-input', 'placeholder'=>'']) !!}
                    </label>
                    <label class="block text-sm">
                        <span class="text-red-700 ">Tomador:</span>
                        {!! Form::text('tomador', $nota->tomador->razao_social, ['disabled', 'class'=>'disabled:opacity-50 block w-full mt-1 text-sm
                          focus:border-purple-400 focus:outline-none focus:shadow-outline-purple
                         :shadow-outline-gray form-input', 'placeholder'=>'']) !!}
                    </label>
                    <label class="block text-sm">
                        <span class="text-red-700 ">Tomador:</span>
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

                    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md ">
                        <div class="bg-gray-50 mt-4 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse">
                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                                <button type="submit" type="button"
                                        class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                                    Cancelar Esta Nota
                                </button>
                            </span>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</x-area-empresa-layout>
