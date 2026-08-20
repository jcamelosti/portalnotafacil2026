{{--@include('components.mensagens')--}}
<div class="flex flex-wrap" id="tabs-id">
    <div class="w-full h-full">
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

                            <label class="block text-sm">
                                <span class="text-gray-700 ">Nbs:</span>
                                {!! Form::select('nbs', []
                                ,null, ['required','disabled', 'id' => 'nbs','class'=>'block w-full mt-1 text-sm  
                                px-3 py-1.5
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                @if ($errors->has('nbs'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('nbs') }}</strong>
                                </span>
                                @endif
                            </label>
                        </div>
                        

                        @if($empresa->regime_tributario === 'simples')
                        <!--div class="grid md:grid-cols-4 gap-1 mt-4 mb-4">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Como IBS/CBS são apurados?:</span>
                                
                            </label>
                        </div-->
                        @endif

                        <div class="grid md:grid-cols-5 gap-1">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Valor Total dos Serviços(*):</span>
                                        {!! Form::text('txtTotal', isset($nota_original) ? number_format($nota_original['txtTotal'],2,',','.') : null, [
                                            'placeholder'=> "0,00",
                                            'name'=>'txtTotal',
                                            'type'=>"text", 'maxlength'=>"50", 'id'=>"txtTotal", 'class'=>"block w-full mt-1 text-sm  
                                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                                    :shadow-outline-gray form-input",
                                            'onkeypress'=>"SoNumeros(event); FormataMoeda(this.name,event);",
                                            'onpaste'=>"return false;",
                                            'onblur'=>""
                                    ])
                                !!}
                                @if ($errors->has('txtTotal'))
                                    <span class="text-xs text-red-600 ">
                                        <strong>{{ $errors->first('txtTotal') }}</strong>
                                    </span>
                                @endif
                            </label>
                            
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Desconto Condic. *:</span>
                                <input name="txtDescontoCondicionado" type="text" maxlength="20" placeholder="0,00" id="txtDescontoCondicionado" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;" onblur="">
                            </label>

                            <label class="block text-sm">
                                <span class="text-gray-700 ">Desconto Incondic. *:</span>
                                <input name="txtDescontoInCondicionado" type="text" maxlength="20" id="txtDescontoInCondicionado" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;" onblur="">
                            </label>
                        </div>

                        <h4 class="mb-4 mt-4 text-base font-semibold text-gray-600 ">
                            Tributação Federal
                        </h4>

                        <div class="grid md:grid-cols-4 gap-1 mt-4 mb-4">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Situação Tributária do PIS/COFINS*:</span>
                                    <select name="ddlSitTribFederal" onchange="" language="javascript" id="ddlSitTribFederal" 
                                    class="block w-full mt-1 text-sm px-3 py-1.5 form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray">
                                        <option selected="selected" value="">Selecione</option>
                                        <option value="00">00 - Nenhum</option>
                                        <option value="01">01 - Operação Tributável com Alíquota Básica</option>
                                        <option value="02">02 - Operação Tributável com Alíquota Diferenciada</option>
                                        <option value="03">03 - Operação Tributável com Alíquota por Unidade de Medida de Produto</option>
                                        <option value="04">04 - Operação Tributável monofásica - Revenda a Alíquota Zero</option>
                                        <option value="05">05 - Operação Tributável por Substituição Tributária</option>
                                        <option value="06">06 - Operação Tributável a Alíquota Zero</option>
                                        <option value="07">07 - Operação Isenta da Contribuição</option>
                                        <option value="08">08 - Operação sem Incidência da Contribuição</option>
                                        <option value="09">09 - Operação com Suspensão da Contribuição</option>
                                        <option value="49">49 - Outras Operações de Saída</option>
                                        <option value="50">50 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                        <option value="51">51 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                        <option value="52">52 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita de Exportação</option>
                                        <option value="53">53 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                        <option value="54">54 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas no Mercado Interno e de Exportação</option>
                                        <option value="55">55 - Operação com Direito a Crédito – Vinculada a Receitas Não Tributadas no Mercado Interno e de Exportação</option>
                                        <option value="56">56 - Operação com Direito a Crédito – Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação</option>
                                        <option value="60">60 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Tributada no Mercado Interno</option>
                                        <option value="61">61 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno</option>
                                        <option value="62">62 - Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita de Exportação</option>
                                        <option value="63">63 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno</option>
                                        <option value="64">64 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas no Mercado Interno e de Exportação</option>
                                        <option value="65">65 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Não-Tributadas no Mercado Interno e de Exportação</option>
                                        <option value="66">66 - Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação</option>
                                        <option value="67">67 - Crédito Presumido – Outras Operações</option>
                                        <option value="70">70 - Operação de Aquisição sem Direito a Crédito</option>
                                        <option value="71">71 - Operação de Aquisição com Isenção</option>
                                        <option value="72">72 - Operação de Aquisição com Suspensão</option>
                                        <option value="73">73 - Operação de Aquisição a Alíquota Zero</option>
                                        <option value="74">74 - Operação de Aquisição sem Incidência da Contribuição</option>
                                        <option value="75">75 - Operação de Aquisição por Substituição Tributária</option>
                                        <option value="98">98 - Outras Operações de Entrada</option>
                                        <option value="99">99 - Outras Operações</option>
                                    </select>
                                    @if ($errors->has('nbs'))
                                        <span class="text-xs text-red-600 ">
                                        <strong>{{ $errors->first('nbs') }}</strong>
                                    </span>
                                    @endif
                            </label>

                            <label class="block text-sm">
                                <span class="text-gray-700 ">Tipo de Retenção do PIS/COFINS/CSLL*:</span>
                                <select name="ddlTipoRetFederal" onchange="" language="javascript" id="ddlTipoRetFederal" 
                                class="block w-full mt-1 text-sm px-3 py-1.5 form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray" onclick="">
                                    <option selected="selected" value="">Selecione</option>
                                    <option value="0">PIS/COFINS/CSLL Não Retidos</option>
                                    <option value="1">PIS/COFINS Retido</option>
                                    <option value="2">PIS/COFINS Não Retido</option>
                                    <option value="3">PIS/COFINS/CSLL Retidos</option>
                                    <option value="4">PIS/COFINS Retidos, CSLL Não Retido</option>
                                    <option value="5">PIS Retido, COFINS/CSLL Não Retido</option>
                                    <option value="6">COFINS Retido, PIS/CSLL Não Retido</option>
                                    <option value="7">PIS Não Retido, COFINS/CSLL Retidos</option>
                                    <option value="8">PIS/COFINS Não Retidos, CSLL Retido</option>
                                    <option value="9">COFINS Não Retido, PIS/CSLL Retidos</option>
                                </select>
                            </label>

                            <div id="divBaseCalcFederal" >
                                <label class="block text-sm">
                                    <span class="text-gray-700 ">Base de Cálculo PIS/COFINS:</span>
                                    <input name="txtBaseCalcFederal" type="text" maxlength="20" placeholder="0,00" id="txtBaseCalcFederal" class="block w-full mt-1 text-sm  
                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                :shadow-outline-gray form-input" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;" onblur="">
                                </label>
                            </div>

                            <div id="divAliqPIS">
                                <label class="block text-sm">
                                    <span class="text-gray-700">Alíquota PIS:</span>
                                    <input name="txtAliqPIS" type="text" maxlength="5" placeholder="0,00" id="txtAliqPIS" class="block w-full mt-1 text-sm  
                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                    :shadow-outline-gray form-input" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;" onblur="">
                                </label>
                            </div>

                            <div id="divAliqCOFINS">
                                <label class="block text-sm">
                                    <span class="text-gray-700">Alíquota COFINS</span>
                                    <input placeholder="0,00" name="txtAliqCOFINS" type="text" maxlength="5" placeholder="0,00" id="txtAliqCOFINS" class="block w-full mt-1 text-sm  
                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                    :shadow-outline-gray form-input" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;" onblur="">
                                </label>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-4 gap-1 mt-4 mb-4">
                            <div id="divValorPis">
                                 <label class="block text-sm">
                                    <span class="text-gray-700">Valor PIS</span>
                                    <input placeholder="0,00" name="txtValorPis" type="text" maxlength="22" id="txtValorPis" disabled="disabled" class="block w-full mt-1 text-sm  
                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                    :shadow-outline-gray form-input" oninput="Money(event);" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;">
                                </label>
                            </div>
                            <div id="divValorCOFINS">
                                <label class="block text-sm">
                                    <span class="text-gray-700">Valor COFINS</span>
                                    <input placeholder="0,00" name="txtValorCOFINS" type="text" maxlength="22" id="txtValorCOFINS" disabled="disabled" class="block w-full mt-1 text-sm  
                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                    :shadow-outline-gray form-input" oninput="Money(event);" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;">
                                </label>
                            </div>
                            <div id="divValorCSLL">
                                <label class="block text-sm">
                                    <span class="text-gray-700">Valor CSLL</span>
                                    <input placeholder="0,00" name="txtValorCSLL" type="text" maxlength="22" onchange="" onkeypress="" language="javascript" id="txtValorCSLL" disabled="disabled" class="block w-full mt-1 text-sm  
                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                    :shadow-outline-gray form-input" oninput="Money(event);" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;">
                                </label>
                            </div>
                            
                            <div id="divValorIRRF">
                                <label class="block text-sm">
                                    <span class="text-gray-700">Valor IRRF</span>
                                    <input placeholder="0,00" name="txtValorIRRF" type="text" maxlength="22" onchange="" onkeypress="" language="javascript" id="txtValorIRRF" class="block w-full mt-1 text-sm  
                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                    :shadow-outline-gray form-input" oninput="Money(event);" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;">
                                </label>
                            </div>
                            <div id="divValorCP">
                                 <label class="block text-sm">
                                    <span class="text-gray-700">Valor CP</span>
                                    <input placeholder="0,00" name="txtValorCP" type="text" maxlength="22" onchange="" language="javascript" id="txtValorCP" class="block w-full mt-1 text-sm  
                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                    :shadow-outline-gray form-input" oninput="Money(event);" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;">
                                 </label>
                            </div>
                        </div>

                        <!--
                            FINAL DO NOVO FORMULÁRIO DAQUI PARA BAIXO                       
                        -->
                        <div class="grid grid-cols-1 gap-1 mt-4">
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



