{{--@include('components.mensagens')--}}
<div class="flex flex-wrap" id="tabs-id">
    <div class="w-full">
        <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
            <div class="px-4 py-5 flex-auto">
                <div class="tab-content tab-space">
                    <div class="block" id="tab-profile">
                        <input type="hidden" value="{{$tomador->id}}" name="tomador_id"/>

                        <!--div class="grid grid-cols-1 gap-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Competência
                            </label>

                            <input 
                                id="data" type="date" value="{{ $data_competencia }}" name="data_competencia"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                placeholder="dd/mm/aaaa"
                            >
                        </div-->

                        <h4 class="mb-4 mt-4 text-base font-semibold text-gray-600 ">
                            Tomador do Serviço: {{ $tomador->razao_social}} - {{ $tomador->cpf_cnpj}}
                        </h4>
                        
                        <h4 class="mb-4 mt-4 text-base font-semibold text-gray-600 ">
                            Descrição dos Serviços
                        </h4>
                        
                        <div class="grid grid-cols-1 gap-1">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Descrição dos Serviços(*) - Caracteres Restantes:</span>
                                <span id="LblLines" class="aspLabel">2000</span>
                                {!!
                                Form::textarea('txtDescServicos', $nota_original['txtDescServicos'] ?? null, [
                                    'name'=>"txtDescServicos",
                                    'id'=>"txtDescServicos",
                                    'style'=>"height: 60px !important;",
                                    'onblur'=>"ReplaceMaxLenght(this, 2000);",
                                    'onkeydown'=>"qtd_caracCont('txtDescServicos', 'LblLines',2000, event)",
                                    'maxlength'=>"2000",

                                    'required',
                                    'class'=>'block w-full mt-1 text-sm  
                                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                                    :shadow-outline-gray form-input',
                                    'placeholder'=>'Descrição do Serviço - Este Campo é Obrigatório',
                                ]) !!}
                            </label>
                        </div>
                        <!--
                            NOVO FORMULÁRIO DAQUI PARA BAIXO                       
                        -->
                        <div class="grid md:grid-cols-4 gap-1 mt-4 mb-4">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Atividade Municipal:</span>
                                {!! Form::select('empresa_atividade_id', isset($atividades) ? $atividades : []
                                ,$nota_original['empresa_atividade_id'] ?? $atividade->codigo_atividade, ['required','class'=>'block w-full mt-1 text-sm  
                                px-3 py-1.5
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray', 'id'=>"empresa_atividade_id"]) !!}
                                @if ($errors->has('empresa_atividade_id'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('empresa_atividade_id') }}</strong>
                                </span>
                                @endif
                            </label>

                            <label class="block text-sm">
                                <span class="text-gray-700 ">Tributação Nacional:</span>
                                {!! Form::select('cTribNac', isset($cod_trib_nac) ? $cod_trib_nac : []
                                ,null, ['required', 'id' => 'cTribNac','class'=>'block w-full mt-1 text-sm  
                                px-3 py-1.5
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                @if ($errors->has('cTribNac'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('cTribNac') }}</strong>
                                </span>
                                @endif
                            </label>
                        </div>
                        <!--
                            FINAL DO NOVO FORMULÁRIO DAQUI PARA BAIXO                       
                        -->
                        <div class="grid grid-cols-1 gap-1">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Informações Complementares(*) - Caracteres Restantes:</span>
                                <span id="LblLines2" class="aspLabel">2000</span>
                                {!!
                                Form::textarea('txtInfoComplementares', $nota_original['txtInfoComplementares'] ?? '.', [
                                    'name'=>"txtInfoComplementares",
                                    'id'=>"txtInfoComplementares",
                                    'style'=>"height: 60px !important;",
                                    'onblur'=>"ReplaceMaxLenght(this, 2000);",
                                    'onkeydown'=>"qtd_caracCont('txtInfoComplementares', 'LblLines2',2000, event)",
                                    'maxlength'=>"2000",
                                    'class'=>'block w-full mt-1 text-sm  
                                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                                    :shadow-outline-gray form-input',
                                    'placeholder'=>'Descrição do Serviço - Este Campo é Obrigatório',
                                ]) !!}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md ">
    <div class="inline-flex">
        <span class="flex rounded-md shadow-sm mr-2">
            <button type="submit" type="button"
                    class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                Transmitir Nota
            </button>
        </span>

        <span class="flex rounded-md shadow-sm">
            <a data-modal-toggle="small-modal" href="{{ route('nota.index') }}"
            class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                Cancelar
            </a>
        </span>
    </div>
</div>



