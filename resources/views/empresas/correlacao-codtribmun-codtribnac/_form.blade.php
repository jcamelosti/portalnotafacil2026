@include('components.mensagens')

<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md ">
    @if (!request()->routeIs('codtrimun-codtribnac.create'))
        <label class="block text-sm">
            <span class="text-gray-700 ">Cód. Interno:</span>
            {!! Form::text('id', null, ['disabled', 'class'=>'disabled:opacity-50 block w-full mt-1 text-sm
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple
             :shadow-outline-gray form-input', 'placeholder'=>'']) !!}
        </label>
    @endif

    <label class="block text-sm">
        <span class="text-gray-700 ">Atividade no Município:</span>
        {!! Form::select('cTribMun', isset($atividades) ? $atividades : []
        ,$correlacaoTribMunTribNac->cTribMun, ['required','class'=>'block w-full mt-1 text-sm  
        form-select
        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
        @if ($errors->has('cTribMun'))
            <span class="text-xs text-red-600 ">
            <strong>{{ $errors->first('cTribMun') }}</strong>
        </span>
        @endif
    </label>

    <label class="block text-sm">
        <span class="text-gray-700 ">Código Tributação Nacional:</span>
        {!! Form::text('cTribNac', null, ['maxlength' => '255','required','class'=>'block w-full mt-1 text-sm  
        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
        :shadow-outline-gray form-input', 'placeholder'=>'Código de Tributação Nacional']) !!}
        @if ($errors->has('cTribNac'))
            <span class="text-xs text-red-600 ">
            <strong>{{ $errors->first('cTribNac') }}</strong>
            </span>
        @endif
    </label>

    <label class="block text-sm">
        <span class="text-gray-700 ">Descrição Tributação Nacional:</span>
        {!! Form::text('xTribNac', null, ['maxlength' => '255','required','class'=>'block w-full mt-1 text-sm  
        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
        :shadow-outline-gray form-input', 'placeholder'=>'Código de Tributação Nacional']) !!}
        @if ($errors->has('xTribNac'))
            <span class="text-xs text-red-600 ">
            <strong>{{ $errors->first('xTribNac') }}</strong>
            </span>
        @endif
    </label>

    <label class="block text-sm">
        <span class="text-gray-700 ">Alíquota(%):</span>
        {!! Form::number('aliquota', null, ['required','class'=>'block w-full mt-1 text-sm  
        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
        :shadow-outline-gray form-input', 'placeholder'=>'Alíquota(%)']) !!}
        @if ($errors->has('aliquota'))
            <span class="text-xs text-red-600 ">
            <strong>{{ $errors->first('aliquota') }}</strong>
            </span>
        @endif
    </label>
</div>

<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto ml-4">
        <a href="{{ route('codtrimun-codtribnac.index', ['empresa' => $empresa]) }}"
           class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
            Cancelar
        </a>
    </span>
    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
        <button type="submit" type="button"
                class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
            Salvar
        </button>
    </span>
</div>

@section('jquery')
@endsection
