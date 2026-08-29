@include('components.mensagens')
<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md ">
    @if (!request()->routeIs('empresa.create'))
        <label class="block text-sm">
            <span class="text-gray-700 ">Empresa Nº:</span>
            {!! Form::text('id', null, ['disabled', 'class'=>'disabled:opacity-50 block w-full mt-1 text-sm
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple
             :shadow-outline-gray form-input', 'placeholder'=>'']) !!}
        </label>
    @endif

    <div class="grid grid-cols-2 gap-1">
        <label class="block text-sm">
            <span class="text-gray-700 ">Razão Social:</span>
            {!! Form::text('razao_social', null, ['maxlength' => '255', 'required', 'class'=>'block w-full mt-1 text-sm  
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
            {!! Form::text('nome_fantasia', null, ['maxlength' => '255','class'=>'block w-full mt-1 text-sm  
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Nome Fantasia']) !!}
            @if ($errors->has('nome_fantasia'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('nome_fantasia') }}</strong>
                </span>
            @endif
        </label>
    </div>

    <div class="grid grid-cols-2 gap-1">
        <label class="block text-sm">
            <span class="text-gray-700 ">CNPJ:</span>
            {!! Form::text('cpf_cnpj', null, ['required','class'=>'block w-full mt-1 text-sm  
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'CNPJ ou CPF da Empresa', 'id' => 'cpf_cnpj']) !!}
            @if ($errors->has('cpf_cnpj'))
            <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('cpf_cnpj') }}</strong>
            </span>
            @endif
        </label>

        <label class="block text-sm">
            <span class="text-gray-700 ">Inscrição Municipal:</span>
            {!! Form::text('inscricao_municipal', null, ['maxlength' => '20','class'=>'block w-full mt-1 text-sm  
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Inscrição Municipal']) !!}
            @if ($errors->has('inscricao_municipal'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('inscricao_municipal') }}</strong>
                </span>
            @endif
        </label>
    </div>

    <div class="grid grid-cols-3 gap-1">
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
        <label class="block text-sm">
            <span class="text-gray-700 ">Telefone Fixo/Celular:</span>
            {!! Form::text('telefone1', !empty($empresa->telefone1) ? $empresa->telefone1 : '', ['required','class'=>'block w-full mt-1 text-sm 
            
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

    <div class="grid grid-cols-3 md:grid-cols-3 lg:grid-cols-3 gap-4 mt-4">
        <div class="lg:col-span-1">
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

        <div class="lg:col-span-2">
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
        </div>

        <div>
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

        <div>
            <label class="block text-sm">
                <span class="text-gray-700 ">Complemento:</span>
                {!! Form::text('complemento', !empty($empresa->complemento) ? $empresa->complemento : '', ['maxlength' => '255','class'=>'block w-full mt-1 text-sm 
                
                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                :shadow-outline-gray form-input', 'placeholder'=>'Complemento']) !!}
                @if ($errors->has('complemento'))
                    <span class="text-xs text-red-600 ">
                    <strong>{{ $errors->first('complemento') }}</strong>
                </span>
                @endif
            </label>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-1 mt-4">
        <label class="block text-sm">
            <span class="text-gray-700 ">Bairro:</span>
            {!! Form::text('bairro', null, ['maxlength' => '255','required','class'=>'block w-full mt-1 text-sm  
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

    <div class="grid grid-cols-1 gap-1 mt-4">
        <label class="block text-sm">
            <span class="text-gray-700 ">Atividade no Município:</span>
            {!! Form::select('empresa_atividade_id', isset($atividades) ? $atividades : []
            ,$empresa->empresa_atividade_id, ['required','class'=>'block w-full mt-1 text-sm  
            
            form-select
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
            @if ($errors->has('empresa_atividade_id'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('empresa_atividade_id') }}</strong>
            </span>
            @endif
        </label>
    <div>

    <div class="grid grid-cols-4 gap-1 mt-4">
        <label class="block text-sm">
            <span class="text-gray-700 ">Empresa é MEI?:</span>
            {!! Form::select('is_mei', [1 => 'SIM', 0 => 'NÃO']
            ,$empresa->is_mei, ['required','class'=>'block w-full mt-1 text-sm  
            
            form-select
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
            @if ($errors->has('is_mei'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('is_mei') }}</strong>
            </span>
            @endif
        </label>
        <label class="block text-sm">
            <span class="text-gray-700 ">Permite Dedução?:</span>
            {!! Form::select('permite_deducao', [1 => 'SIM', 2 => 'NÃO']
            ,$empresa->permite_deducao, ['required','class'=>'block w-full mt-1 text-sm  
            
            form-select
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
            @if ($errors->has('permite_deducao'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('permite_deducao') }}</strong>
            </span>
            @endif
        </label>
        <label class="block text-sm">
            <span class="text-gray-700 ">Permite Desc. Incond.?:</span>
            {!! Form::select('permite_deducao', [1 => 'SIM', 2 => 'NÃO']
            ,$empresa->permite_desc_incond, ['required','class'=>'block w-full mt-1 text-sm  
            
            form-select
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
            @if ($errors->has('permite_desc_incond'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('permite_desc_incond') }}</strong>
            </span>
            @endif
        </label>
        <label class="block text-sm">
            <span class="text-gray-700 ">Permite Desc. Cond.?:</span>
            {!! Form::select('permite_deducao', [1 => 'SIM', 2 => 'NÃO']
            ,$empresa->permite_desc_cond, ['required','class'=>'block w-full mt-1 text-sm  
            
            form-select
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
            @if ($errors->has('permite_desc_cond'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('permite_desc_cond') }}</strong>
            </span>
            @endif
        </label>
    </div>

    <div class="grid grid-cols-4 gap-1 mt-4">
        <label class="block text-sm">
            <span class="text-gray-700 ">Optante Simples Nacional?:</span>
            {!! Form::select('is_optante_simples_nac', [1 => 'SIM', 2 => 'NÃO']
            ,$empresa->is_optante_simples_nac, ['required','class'=>'block w-full mt-1 text-sm  
            
            form-select
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
            @if ($errors->has('is_optante_simples_nac'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('is_optante_simples_nac') }}</strong>
            </span>
            @endif
        </label>
        <label class="block text-sm">
            <span class="text-gray-700 ">Porte:</span>
            {!! Form::text('porte_empresa', null, ['maxlength' => '255','readonly','class'=>'block w-full mt-1 text-sm 
            
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Porte']) !!}
            @if ($errors->has('complemento'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('complemento') }}</strong>
            </span>
            @endif
        </label>
        <label class="block text-sm">
            <span class="text-gray-700 ">Natureza Jurídica:</span>
            {!! Form::text('natureza_juridica', null, ['maxlength' => '255','readonly', 'class'=>'block w-full mt-1 text-sm 
            
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Natureza Jurídica']) !!}
            @if ($errors->has('complemento'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('complemento') }}</strong>
            </span>
            @endif
        </label>
    </div>

    <h2 class="mt-4 mb-4 text-2xl font-semibold text-blue-700">Campos importantes para NFSe Nacional</h2>

    <div class="grid grid-cols-4 gap-1">
        <label class="block text-sm">
            <span class="text-gray-700 ">Provedor de Emissão NFS-e:</span>
            {!! Form::select('sigla_provedor', $provedores
            ,$empresa->sigla_provedor, ['required','class'=>'block w-full mt-1 text-sm  
            form-select
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
            @if ($errors->has('sigla_provedor'))
                <span class="text-xs text-red-600 ">
                <strong>{ { $errors->first('sigla_provedor') }}</strong>
            </span>
            @endif
        </label>

        <label class="block text-sm">
            <span class="text-gray-700 ">Ambiente Emissão NFS-e:</span>
            {!! Form::select('ambiente_emissao', $ambientes_emissao
            ,$empresa->ambiente_emissao, ['required','class'=>'block w-full mt-1 text-sm  
            form-select
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
            @if ($errors->has('ambiente_emissao'))
                <span class="text-xs text-red-600 ">
                <strong>{ { $errors->first('ambiente_emissao') }}</strong>
            </span>
            @endif
        </label>
    </div>

    <div class="grid grid-cols-4 gap-1 mt-4">
        <label class="block text-sm">
            <span class="text-gray-700 ">Situação Perante Simples Nacional</span>
            {!! Form::select('op_simp_nac', $situacao_simples_nacional
            ,$empresa->op_simp_nac, ['required','class'=>'block w-full mt-1 text-sm  
            form-select
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
            @if ($errors->has('op_simp_nac'))
                <span class="text-xs text-red-600 ">
                <strong>{ { $errors->first('op_simp_nac') }}</strong>
            </span>
            @endif
        </label>

        @if($empresa->op_simp_nac == 3 || $empresa->op_simp_nac == 2)
        <label class="block text-sm">
            <span class="text-gray-700 ">Regime de Apuração Tributária pelo Simples Nacional:</span>
            {!! Form::select('tp_reg_apuracao_sn', $regimes_apuracao_sn
            ,$empresa->tp_reg_apuracao_sn, ['required','class'=>'block w-full mt-1 text-sm  
            form-select
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
            @if ($errors->has('tp_reg_apuracao_sn'))
                <span class="text-xs text-red-600 ">
                <strong>{ { $errors->first('tp_reg_apuracao_sn') }}</strong>
            </span>
            @endif
        </label>
        @endif

        <label class="block text-sm">
            <span class="text-gray-700 ">Tipos de Regimes Especiais de Tributação Municipal:</span>
            {!! Form::select('tp_regime_esp_trib_mun', $tipos_regime_esp_trib_mun
            ,$empresa->tp_regime_esp_trib_mun, ['required','class'=>'block w-full mt-1 text-sm  
            form-select
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
            @if ($errors->has('tp_regime_esp_trib_mun'))
                <span class="text-xs text-red-600 ">
                <strong>{ { $errors->first('tp_regime_esp_trib_mun') }}</strong>
            </span>
            @endif
        </label>
    </div>

    <div class="grid grid-cols-4 gap-1 mt-6">
        <label class="block text-sm">
            <span class="text-gray-700 ">Plano:</span>
            {!! Form::select('plano_id', $planos
            ,$empresa->plano_id, ['required','class'=>'block w-full mt-1 text-sm  
            
            form-select
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
            @if ($errors->has('plano_id'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('plano_id') }}</strong>
            </span>
            @endif
        </label>
    </div>

    <div class="grid grid-cols-1 gap-1">
        <label class="block text-sm">
            <span class="text-gray-700 ">Responsável:</span>
            {!! Form::select('user_id', $usuarios
            ,$empresa->user_id, ['required','class'=>'select2 block w-full mt-1 text-sm  
            
            form-select
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
            @if ($errors->has('user_id'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('user_id') }}</strong>
            </span>
            @endif
        </label>
    <div>
    
    <div class="grid grid-cols-2 gap-1 mt-4">
        <label class="block text-sm">
            <span class="text-gray-700 ">Próximo Número de DPS(Número do próximo DPS NFSe Nacional):</span>
            {!! Form::text('num_ultimo_dps', null, ['maxlength' => '6','class'=>'block w-full mt-1 text-sm  
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Se não Possui deixe em Vazio','id'=>'number']) !!}
            @if ($errors->has('num_ultimo_dps'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('num_ultimo_dps') }}</strong>
            </span>
            @endif
        </label>

        <label class="block text-sm">
            <span class="text-gray-700 ">Série DPS:</span>
            {!! Form::text('serie_dps', null, ['maxlength' => '6','class'=>'block w-full mt-1 text-sm  
            focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
            :shadow-outline-gray form-input', 'placeholder'=>'Se não Possui deixe em Vazio','id'=>'number']) !!}
            @if ($errors->has('serie_dps'))
                <span class="text-xs text-red-600 ">
                <strong>{{ $errors->first('serie_dps') }}</strong>
            </span>
            @endif
        </label>
    </div>
</div>

<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse mt-4">
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
    <!--script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-masker/1.2.0/vanilla-masker.min.js"
            integrity="sha512-RbMQw6xKGymv6bRMO4z5OxHBzzem7BPEQX7nTJC9G08A70gXdUka76Rvgey83MsSXrIEJddog0vxUKN6iTce2Q=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.es.gov.br/scripts/jquery/jquery-maskedinput/1.4.1/jquery.maskedinput-1.4.1.min.js"></script>

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

        //tratamentos mascaras
        //Telefone
        var telMask = ['(99) 9999-99999', '(99) 99999-9999'];
        var tel = document.querySelector('#phone_fixo');
        VMasker(tel).maskPattern(telMask[0]);
        tel.addEventListener('input', inputHandler.bind(undefined, telMask, 14), false);

        var tel2 = document.querySelector('#whatsapp');
        VMasker(tel2).maskPattern(telMask[0]);
        tel2.addEventListener('input', inputHandler.bind(undefined, telMask, 14), false);

        //Cep
        var cepMask = ['99999-999'];
        var cep = document.querySelector('#cep');
        VMasker(cep).maskPattern(cepMask[0]);
        cep.addEventListener('input', inputHandler.bind(undefined, cepMask, 14), false);

        //CPF/CNPJ
        var docMask = ['99.999.999/9999-99', '99.999.999/9999-99'];
        var doc1 = document.querySelector('#cpf_cnpj');
        var doc2 = document.querySelector('#cpf_cnpj');
        if(doc1 != null) {
            if(doc1.value.length <= 14){
                VMasker(doc1).maskPattern(docMask[0]);
                doc1.addEventListener('input', inputHandler.bind(undefined, docMask, 14), false);
                triggerEvent(doc1, 'keyup');
            }
        }

        if(doc2 != null) {
            VMasker(doc2).maskPattern(docMask[1]);
            if(doc2.value.length == 18) {
                doc2.addEventListener('input', inputHandler.bind(undefined, docMask, 18), false);
                triggerEvent(doc1, 'keyup');
            }
        }
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
                                $("input#city").val(result.city);
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
