<!--INICIO FORM -->

@include('components.mensagens')
<div class="w-full px-4 py-3 mb-8 bg-white rounded-lg shadow-md ">
    @if (!request()->routeIs('empresas-certificados.create'))
        <label class="block text-sm">
            <span class="text-gray-700 ">ID:</span>
            {!! Form::text('id', isset($certificado) ? $certificado->id : null, ['disabled', 'class'=>'disabled:opacity-50 block w-full mt-1 text-sm
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple
             :shadow-outline-gray form-input', 'placeholder'=>'']) !!}
        </label>
    @endif

    <label class="block text-sm w-10/12" id="">
        <span class="text-gray-700 ">Empresa:</span>
        {!! Form::select('empresa_id', $empresas, isset($certificado) ? $certificado->empresa_id : null, ['required','class'=>'block w-full mt-1 text-sm  
        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
        :shadow-outline-gray form-input', 'placeholder'=>'', 'id' => 'empresa_id']) !!}
        @if ($errors->has('empresa_id'))
            <span class="text-xs text-red-600 ">
            <strong>{{ $errors->first('empresa_id') }}</strong>
        </span>
        @endif
    </label>

    <label class="block text-sm w-10/12" id="">
        <span class="text-gray-700 ">Certificado:</span>
        {!! Form::file('certificado', ['accept' => '.pfx', 'required','class'=>'block w-full mt-1 text-sm  
        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
        :shadow-outline-gray form-input']) !!}
        @if ($errors->has('certificado'))
            <span class="text-xs text-red-600 ">
            <strong>{{ $errors->first('certificado') }}</strong>
        </span>
        @endif
    </label>

    <div class="grid grid-cols-2 gap-1">
        <label class="block text-sm">
            <span class="text-gray-700 ">Senha:</span>
            <input type="password" autocomplete="off" required name="password" 
                class="block w-full mt-1 text-sm  
                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                :shadow-outline-gray form-input" 
                id="nova-senha">
            @if ($errors->has('senha'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('senha') }}</strong>
            </span>
            @endif
        </label>

        <label class="block text-sm">
            <span class="text-gray-700 ">Confirmação Senha:</span>
            <input type="password" autocomplete="off" required name="password_confirmation" class="block w-full mt-1 text-sm  
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input" id="repetir-senha">
            @if ($errors->has('senha'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('senha') }}</strong>
            </span>
            @endif
        </label>
    </div>
</div>

<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
        <a href="{{ route('empresas-certificados.index') }}"
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