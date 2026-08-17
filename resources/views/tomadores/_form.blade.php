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
        <span class="text-gray-700 ">CNPJ:</span>
        {!! Form::text('cpf_cnpj', !empty($empresa->cpf_cnpj) ? $empresa->cpf_cnpj_fmt : '', [
            'onkeypress'=> "Formata_Cpf_Cnpj(this.name,event);",
            'onkeyup'=>"Formata_Cpf_Cnpj(this.name,event);", 
            'onblur'=> "Valida_Cpf_Cnpj(this.name,event);",
            'onpaste'=>"return true;",
            'id' => 'txtCpfCnpj','required','class'=>'block w-full mt-1 text-sm  
        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
        :shadow-outline-gray form-input', 'placeholder'=>'CNPJ ou CPF do Tomador. Obs. Os Dados para CNPJ serão buscados conforme cadastro na receita federal.']) !!}
        @if ($errors->has('cpf_cnpj'))
            <span class="text-xs text-red-600 ">
            <strong>{{ $errors->first('cpf_cnpj') }}</strong>
        </span>
        @endif
    </label>

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
    <div class="grid grid-cols-2 gap-1">
        <label class="block text-sm">
            <span class="text-gray-700 ">Inscrição Municipal:</span>
            {!! Form::text('inscricao_municipal', !empty($empresa->inscricao_municipal) ? $empresa->inscricao_municipal : '', ['maxlength' => 15,'class'=>'block w-full mt-1 text-sm 
            
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Inscrição Municipal', 'id'=>'inscricao_municipal']) !!}
            @if ($errors->has('inscricao_municipal'))
                <span class="text-xs text-red-600 ">
                    <strong>{{ $errors->first('inscricao_municipal') }}</strong>
                </span>
            @endif
        </label>

        {{--<label class="block text-sm">
            <span class="text-gray-700 ">Inscrição Estadual</span>
            {!! Form::text('telefone2', !empty($empresa->telefone2) ? $empresa->telefone2 : '', ['class'=>'block w-full mt-1 text-sm  
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Informe o número de WhatsApp', 'id'=>'whatsapp']) !!}
            @if ($errors->has('telefone2'))
                <span class="text-xs text-red-600 ">
                    <strong>{{ $errors->first('telefone2') }}</strong>
                </span>
            @endif
        </label>--}}
    </div>
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

        <label class="block text-sm">
            <span class="text-gray-700 ">WhatsApp:</span>
            {!! Form::text('telefone2', !empty($empresa->telefone2) ? $empresa->telefone2 : '', ['class'=>'block w-full mt-1 text-sm  
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Informe o número de WhatsApp', 'id'=>'whatsapp']) !!}
            @if ($errors->has('telefone2'))
                <span class="text-xs text-red-600 ">
                    <strong>{{ $errors->first('telefone2') }}</strong>
                </span>
            @endif
        </label>
    </div>

    <div class="grid grid-cols-2 gap-1">
        <label class="block text-sm">
            <span class="text-gray-700 ">CEP:</span>
            {!! Form::text('cep', !empty($empresa->cep) ? $empresa->cep : '', ['required','class'=>'cep block w-full mt-1 text-sm  
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Informe o CEP é o Endereço será Buscado Automaticamente', 'id'=>'cep']) !!}
            @if ($errors->has('cep'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('cep') }}</strong>
            </span>
            @endif
        </label>
    </div>

    <div class="grid grid-cols-2 gap-1">
        <label class="block text-sm">
            <span class="text-gray-700 ">Logradouro:</span>
            {!! Form::text('logradouro', !empty($empresa->logradouro) ? $empresa->logradouro : '', ['required','class'=>'block w-full mt-1 text-sm  
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Endereço da Empresa: Rua X, Quadra Y Lote B', 'id'=>'street']) !!}
            @if ($errors->has('logradouro'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('logradouro') }}</strong>
            </span>
            @endif
        </label>

        <label class="block text-sm">
            <span class="text-gray-700 ">Número:</span>
            {!! Form::text('numero', !empty($empresa->numero) ? $empresa->numero : ' ', ['maxlength' => '6','class'=>'block w-full mt-1 text-sm  
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Se não Possui deixe em Vazio','id'=>'number']) !!}
            @if ($errors->has('numero'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('numero') }}</strong>
            </span>
            @endif
        </label>
    </div>
    <div class="grid grid-cols-1 gap-1">
        <label class="block text-sm">
            <span class="text-gray-700 ">Complento:</span>
            {!! Form::text('complemento', !empty($empresa->complemento) ? $empresa->complemento : '', ['maxlength' => '60','class'=>'block w-full mt-1 text-sm 
            
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Complemento']) !!}
            @if ($errors->has('complemento'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('complemento') }}</strong>
            </span>
            @endif
        </label>
    </div>
    <div class="grid grid-cols-4 gap-1">
        <label class="block text-sm">
            <span class="text-gray-700 ">Bairro:</span>
            {!! Form::text('bairro', !empty($empresa->bairro) ? $empresa->bairro : '', ['maxlength' => '255','required','class'=>'block w-full mt-1 text-sm  
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Bairro', 'id'=>'district']) !!}
            @if ($errors->has('bairro'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('bairro') }}</strong>
            </span>
            @endif
        </label>

        <label class="block text-sm">
            <span class="text-gray-700 ">UF:</span>
            {!! Form::select('uf',
            $estados
            ,$uf_id, ['id'=>'uf_id', 'required','class'=>'select2 block w-full mt-1 text-sm  
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
                {!! Form::select('cidade_id', $cidades, $empresa->cidade_id, ['maxlength' => '255','required','class'=>'select2 block w-full mt-1 text-sm  
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/fn_geral.js') }}"></script>
    <script type="text/javascript">
        function triggerEvent(el, type) {
            if ('createEvent' in document) {
                var e = document.createEvent('HTMLEvents');
                e.initEvent(type, false, true);
                el.dispatchEvent(e);
            }
        }

       function inputHandler(masks, max, event) {
            var c = event.target;
            var v = c.value.replace(/\D/g, '');
            var m = c.value.length > max ? 1 : 0;
            VMasker(c).unMask();
            VMasker(c).maskPattern(masks[m]);
            c.value = VMasker.toPattern(v, masks[m]);
        }

        //Cep
        var cepMask = ['99999-999'];
        var cep = document.querySelector('#cep');
        VMasker(cep).maskPattern(cepMask[0]);
        cep.addEventListener('input', inputHandler.bind(undefined, cepMask, 14), false);

        //tratamentos mascaras
        //Telefone
        var telMask = ['(99) 9999-99999', '(99) 99999-9999'];
        var tel = document.querySelector('#phone_fixo');
        VMasker(tel).maskPattern(telMask[0]);
        tel.addEventListener('input', inputHandler.bind(undefined, telMask, 14), false);

        var tel2 = document.querySelector('#whatsapp');
        VMasker(tel2).maskPattern(telMask[0]);
        tel2.addEventListener('input', inputHandler.bind(undefined, telMask, 14), false);
    </script>
    <script type="text/javascript">
        $(document).ready(function(){
            var somenteNumeros = function(valor){
                return valor.replace(/\D/g, '');
            };
            var trim = function(valor){
                return valor.replace(" ", "");
            };

            var consultaCep1 = function(cep){
                if(cep != '7500-000' && cep.length >= 9) {
                    $.getJSON(
                        "//cdn.apicep.com/file/apicep/" + cep + ".json",
                        function(result) {
                            if (result.status === 200) {
                                //$("input#cidade_id").val(result.city).trigger('change');
                                $("input#district").val(result.district);
                                $("input#street").val(result.address);
                                $('#uf').val(result.state);
                                console.log('Achou o CEP na API CEP.');
                            } 
                        }
                    ).fail(function() {
                        console.log('Erro API CEP.');
                        //buscarViaCep(cep);
                    });
                }
            };

            $('#cep').on('keyup', function(e){
                var valor = $(this).val();
                //Nova variável "cep" somente com dígitos.
                var cep = somenteNumeros(valor);

                //Verifica se campo cep possui valor informado.
                if (cep != "") {
                    //Expressão regular para validar o CEP.
                    var validacep = /^[0-9]{8}$/;
                    //Valida o formato do CEP.
                    if(validacep.test(cep)) {consultaCep1(valor);}
                }
            });

            $('#cep').on('blur', function(e){
                var valor = $(this).val();
                //Nova variável "cep" somente com dígitos.
                var cep = somenteNumeros(valor);

                //Verifica se campo cep possui valor informado.
                if (cep != "") {
                    //Expressão regular para validar o CEP.
                    var validacep = /^[0-9]{8}$/;
                    //Valida o formato do CEP.
                    if(validacep.test(cep)) {
                        consultaCep1(valor);
                    }
                }
            });
        });
    </script>
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
