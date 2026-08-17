@include('components.mensagens')
<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md ">
    @if (!request()->routeIs('tomadores.create'))
        <label class="block text-sm">
            <span class="text-gray-700 ">Tomador Nº:</span>
            {!! Form::text('id', null, ['disabled', 'class'=>'disabled:opacity-50 block w-full mt-1 text-sm
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple
             :shadow-outline-gray form-input', 'placeholder'=>'']) !!}
        </label>
    @endif

    <label class="block text-sm">
        <span class="text-gray-700 ">Razão Social:</span>
        {!! Form::text('razao_social', !empty($empresa->razao_social) ? $empresa->razao_social : '', ['maxlength' => '100','required','class'=>'block w-full mt-1 text-sm  
        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
        :shadow-outline-gray form-input', 'placeholder'=>'Razão Social']) !!}
        @if ($errors->has('razao_social'))
            <span class="text-xs text-red-600 ">
            <strong>{{ $errors->first('razao_social') }}</strong>
            </span>
        @endif
    </label>
    <label class="block text-sm">
        <span class="text-gray-700 ">Nome Fantasia:</span>
        {!! Form::text('nome_fantasia', !empty($empresa->nome_fantasia) ? $empresa->nome_fantasia : '', ['maxlength' => '255','class'=>'block w-full mt-1 text-sm  
        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
        :shadow-outline-gray form-input', 'placeholder'=>'Nome Fantasia']) !!}
        @if ($errors->has('nome_fantasia'))
            <span class="text-xs text-red-600 ">
            <strong>{{ $errors->first('nome_fantasia') }}</strong>
            </span>
        @endif
    </label>
    <label class="block text-sm">
        <span class="text-gray-700 ">E-mail para Contato:</span>
        {!! Form::text('email', !empty($empresa->email) ? $empresa->email : '', ['maxlength' => '255','class'=>'block w-full mt-1 text-sm  
        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
        :shadow-outline-gray form-input', 'placeholder'=>'emaildaempresa@provedor.com.br']) !!}
        @if ($errors->has('email'))
            <span class="text-xs text-red-600 ">
            <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
    </label>

    <div class="grid grid-cols-2 gap-1">
        <label class="block text-sm">
            <span class="text-gray-700 ">Telefone Fixo/Celular:</span>
            {!! Form::text('telefone1', !empty($empresa->telefone1) ? $empresa->telefone1 : '', ['class'=>'block w-full mt-1 text-sm 
            
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Informe o telefone fixo ou celular de contato', 'id'=>'phone_fixo']) !!}
            @if ($errors->has('telefone1'))
                <span class="text-xs text-red-600 ">
                    <strong>{{ $errors->first('telefone1') }}</strong>
                </span>
            @endif
        </label>
    </div>

    <div class="grid grid-cols-2 gap-1">
        <label class="block text-sm">
            <span class="text-gray-700 ">Código do Pais(Conforme Bacen):</span>
            {!! Form::text('codigo_pais_bacen', !empty($empresa->codigo_pais_bacen) ? $empresa->codigo_pais_bacen : '', ['required','class'=>'block w-full mt-1 text-sm 
            
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'0000', 'id'=>'codigo_pais_bacen']) !!}
            @if ($errors->has('codigo_pais_bacen'))
                <span class="text-xs text-red-600 ">
                    <strong>{{ $errors->first('codigo_pais_bacen') }}</strong>
                </span>
            @endif
        </label>
    </div>

    <div class="grid grid-cols-2 gap-1">
        <label class="block text-sm">
            <span class="text-gray-700 ">NIF:</span>
            {!! Form::text('nif', !empty($empresa->nif) ? $empresa->nif : '', ['required','class'=>'block w-full mt-1 text-sm 
            
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'0000', 'id'=>'nif']) !!}
            @if ($errors->has('codigo_pais_bacen'))
                <span class="text-xs text-red-600 ">
                    <strong>{{ $errors->first('nif') }}</strong>
                </span>
            @endif
        </label>
    </div>

    <div class="grid grid-cols-2 gap-1">
        <label class="block text-sm">
            <span class="text-gray-700 ">Endereço Completo(Exterior):</span>
            {!! Form::text('logradouro', !empty($empresa->logradouro) ? $empresa->logradouro : '', ['required','class'=>'block w-full mt-1 text-sm  
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Endereço da Empresa: Rua X, Quadra Y Lote B', 'id'=>'street']) !!}
            @if ($errors->has('logradouro'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('logradouro') }}</strong>
            </span>
            @endif
        </label>
    </div>
    <div class="grid grid-cols-4 gap-1">
        <label class="block text-sm">
            <span class="text-gray-700 ">UF:</span>
            {!! Form::select('uf',
            $estados
            ,$uf_id, ['id'=>'uf_id', 'required','class'=>'block w-full mt-1 text-sm  
            
            form-select
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
            @if ($errors->has('uf'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('uf') }}</strong>
            </span>
            @endif
        </label>
        <div id="containerCidade">
            <label class="block text-sm w-10/12" id="cidade_id">
                <span class="text-gray-700 ">Cidade:</span>
                {!! Form::select('cidade_id', $cidades, $empresa->cidade_id, ['maxlength' => '255','required','class'=>'block w-full mt-1 text-sm  
                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                :shadow-outline-gray form-input', 'placeholder'=>'', 'id' => 'cidade_id']) !!}
                @if ($errors->has('cidade_id'))
                    <span class="text-xs text-red-600 ">
                    <strong>{{ $errors->first('cidade_id') }}</strong>
                </span>
                @endif
            </label>
        </div>
    </div>
</div>

<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
        <a href="{{ route('empresas.index') }}"
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-masker/1.2.0/vanilla-masker.min.js"
            integrity="sha512-RbMQw6xKGymv6bRMO4z5OxHBzzem7BPEQX7nTJC9G08A70gXdUka76Rvgey83MsSXrIEJddog0vxUKN6iTce2Q=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.es.gov.br/scripts/jquery/jquery-maskedinput/1.4.1/jquery.maskedinput-1.4.1.min.js"></script>
    <script src="{{ asset('js/fn_geral.js') }}"></script>
    <script>
        $(function() {
            $('.date').mask('99/99/9999');
        });
    </script>
    
    <script type="text/javascript">
        $(function(){
            $('#uf_id').change(function(e){
                $('#cidade_id').remove();
                if( $(this).val() != '' ) {
                    e.preventDefault();

                    $.ajax({
                    type      : 'GET',
                    url: base_url + '/consultar/cidades/' + $(this).val(),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success : function(result){
                        $('#cidade_id').remove();
                        $('#containerCidade').append(result);
                    },
                    error : function(){
                        alert('Verifique os dados Informados e tente novamente.');
                    }
                    });
                } else {
                    //$('.comboBoxCidades1').show();
                    //$('.comboBoxCidades1').html('<option value="">Escolha o Estado</option>');
                }
            });
        });
    </script>
@endsection
