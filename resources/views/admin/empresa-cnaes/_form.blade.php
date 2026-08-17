@include('components.mensagens')
<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md ">
    @if (!request()->routeIs('admin.empresa-cnaes.create'))
        <label class="block text-sm">
            <span class="text-gray-700 ">CNAE Nº:</span>
            {!! Form::text('id', null, ['disabled', 'class'=>'disabled:opacity-50 block w-full mt-1 text-sm
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple
             :shadow-outline-gray form-input', 'placeholder'=>'']) !!}
        </label>
    @endif

    <label class="block text-sm">
        <span class="text-gray-700 ">Código CNAE:</span>
        {!! Form::text('codigo_cnae', null, ['maxlength' => '255','required','class'=>'block w-full mt-1 text-sm  
        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
        :shadow-outline-gray form-input', 'placeholder'=>'Código CNAE']) !!}
        @if ($errors->has('codigo_cnae'))
            <span class="text-xs text-red-600 ">
            <strong>{{ $errors->first('codigo_cnae') }}</strong>
            </span>
        @endif
    </label>
    <label class="block text-sm">
        <span class="text-gray-700 ">Descrição CNAE:</span>
        {!! Form::text('descricao_cnae', null, ['maxlength' => '255','required','class'=>'block w-full mt-1 text-sm  
        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
        :shadow-outline-gray form-input', 'placeholder'=>'Descrição CNAE']) !!}
        @if ($errors->has('descricao_cnae'))
            <span class="text-xs text-red-600 ">
            <strong>{{ $errors->first('descricao_cnae') }}</strong>
            </span>
        @endif
    </label>
    <label class="block text-sm">
        <span class="text-gray-700 ">Principal?:</span>
        {!! Form::select('principal', [1 => 'SIM', 2 => 'NÃO']
        ,$cnae->principal, ['required','class'=>'block w-full mt-1 text-sm  
        
        form-select
        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
        @if ($errors->has('principal'))
            <span class="text-xs text-red-600 ">
            <strong>{{ $errors->first('principal') }}</strong>
        </span>
        @endif
    </label>
</div>

<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
        <a href="{{ route('admin.empresa-cnaes.index', ['empresa-id' => $cnae->empresa_id ]) }}"
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
