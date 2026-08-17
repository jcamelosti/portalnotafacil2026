{{--@include('components.mensagens')--}}
<div class="flex flex-wrap" id="tabs-id">
    <div class="w-full">
        <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
            <div class="px-4 py-5 flex-auto">
                <div class="tab-content tab-space">
                    <div class="block" id="tab-profile">
                        <input type="hidden" value="8" name="serienota"/>
                        <input type="hidden" value="1" name="ddlNaturezaOperacao"/>
                        <input type="hidden" value="{{$tomador->id}}" name="tomador_id"/>

                        @if (request()->routeIs('notas.substitucao'))
                            <div class="mb-6">
                                <h2 class="mb-4 text-lg font-semibold text-gray-600 ">
                                    Nota Fiscal de Serviço - Substituição de NFS-e
                                </h4>
                                <input type="hidden" name="nota_emitida_substituida_id" value="{{ $nfse->id }}" />
                                <div class="grid grid-cols-2 gap-1 localPrestacaoServico">
                                    <label class="block text-sm">
                                        <span class="text-gray-700 ">Nota para Substituição:</span>
                                        {!! Form::text('num_nfse', $nfse->num_nfse, [
                                            'placeholder'=> "",
                                            'name'=>'num_nfse',
                                            'type'=>"text", 'maxlength'=>"50", 'id'=>"num_nfse", 'class'=>"block w-full mt-1 text-sm  
                                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                                    :shadow-outline-gray form-input", 'readonly'])
                                        !!}
                                        @if ($errors->has('txtTotal'))
                                            <span class="text-xs text-red-600 ">
                                                <strong>{{ $errors->first('txtTotal') }}</strong>
                                            </span>
                                        @endif
                                    </label>

                                    <label class="block text-sm">
                                        <span class="text-gray-700 ">Série Nota para Substituição:</span>
                                        {!! Form::text('serie_rps', $nfse->serie_rps, [
                                            'placeholder'=> "",
                                            'name'=>'serie_rps',
                                            'type'=>"text", 'maxlength'=>"50", 'id'=>"serie_rps", 'class'=>"block w-full mt-1 text-sm  
                                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                                    :shadow-outline-gray form-input", 'readonly'])
                                        !!}
                                        @if ($errors->has('txtTotal'))
                                            <span class="text-xs text-red-600 ">
                                                <strong>{{ $errors->first('txtTotal') }}</strong>
                                            </span>
                                        @endif
                                    </label>
                                </div>
                                <div class="grid grid-cols-2 gap-1 localPrestacaoServico">
                                    <label class="block text-sm">
                                        <span class="text-red-700  text-2x">Motivo do Cancelamento:</span>
                                        {!! Form::select('motivo', $motivo, null, ['required','class'=>'block w-full mt-1 text-sm  
                                        
                                        form-select
                                        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                        @if ($errors->has('motivo'))
                                            <span class="text-xs text-red-600 ">
                                            <strong>{{ $errors->first('motivo') }}</strong>
                                        </span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        @endif
                        
                        <div class="grid grid-cols-1 gap-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Competência
                            </label>

                            <input 
                                id="data" type="date" value="{{ $data_competencia }}" name="data_competencia"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                placeholder="dd/mm/aaaa"
                            >
                        </div>

                        <h4 class="mb-4 text-lg font-semibold text-gray-600 ">
                            Local de Prestação do Serviço
                        </h4>
                    
                        <div class="grid grid-cols-2 gap-1 localPrestacaoServico">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">UF:</span>
                                {!! Form::select('uf',
                                $estados
                                ,$uf_id, ['id' => 'uf_id','required','class'=>'block w-full mt-1 text-sm  
                                
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                @if ($errors->has('uf'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('uf') }}</strong>
                                </span>
                                @endif
                            </label>
                    
                            <label class="block text-sm w-10/12" id="cidade_id">
                                <span class="text-gray-700 ">Cidade:</span>
                                {!! Form::select('cidade_id', $cidades, $empresa->cidade_id, ['maxlength' => '255','required','class'=>'block w-full mt-1 text-sm  
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                                :shadow-outline-gray form-input', 'placeholder'=>'', 'id'=>'city']) !!}
                                @if ($errors->has('cidade_id'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('cidade_id') }}</strong>
                                </span>
                                @endif
                            </label>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-1">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Natureza Operação:</span>
                                {!! Form::select('ddlNaturezaOperacao', $listTributacao, $nota_original['ddlNaturezaOperacao'] ?? 1, ['id' => 'ddlNaturezaOperacao','required','class'=>'block w-full mt-1 text-sm  
                                
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                @if ($errors->has('uf'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('ddlNaturezaOperacao') }}</strong>
                                </span>
                                @endif
                            </label>
                        </div>

                        <h4 class="mb-4 mt-4 text-lg font-semibold text-gray-600 ">
                            Tomador
                        </h4>
                        
                        {{ $tomador->razao_social}} - {{ $tomador->cpf_cnpj}}

                        <h4 class="mb-4 mt-4 text-lg font-semibold text-gray-600 ">
                            Descrição dos Serviços
                        </h4>
                        
                        <div class="grid md:grid-cols-4 gap-1 mt-4 mb-4">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Cnae:</span>
                                {!! Form::select('empresa_cnae_id', isset($cnaes) ? $cnaes : []
                                ,$nota_original['empresa_cnae_id'] ?? $empresa->empresa_cnae_id, ['required',"id"=>'empresa_cnae_id' ,'class'=>'block w-full mt-1 text-sm  
                                
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                @if ($errors->has('empresa_cnae_id'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('empresa_cnae_id') }}</strong>
                                </span>
                                @endif
                            </label>

                            <label class="block text-sm">
                                <span class="text-gray-700 ">Atividade no Município:</span>
                                {!! Form::select('empresa_atividade_id', isset($atividades) ? $atividades : []
                                ,$nota_original['empresa_atividade_id'] ?? $empresa->empresa_atividade_id, ['required','class'=>'block w-full mt-1 text-sm  
                                
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                @if ($errors->has('empresa_atividade_id'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('empresa_atividade_id') }}</strong>
                                </span>
                                @endif
                            </label>

                            @if(!empty($empresa->empresa_cnae_id))
                                <span id="item_lc_container">
                                    <label class="block text-sm">
                                        <span class="text-gray-700 ">Item da LC116/2003:</span>
                                        {!! Form::select('item_lc_id', $servicos
                                        ,$nota_original['item_lc_id'] ?? $empresa->item_lc_id, [empty($servicos) ? '' : 'required','class'=>'block w-full mt-1 text-sm  
                                        
                                        form-select
                                        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray', 'id' => 'item_lc_id']) !!}
                                        @if ($errors->has('item_lc_id'))
                                            <span class="text-xs text-red-600 ">
                                            <strong>{{ $errors->first('item_lc_id') }}</strong>
                                        </span>
                                        @endif
                                    </label>
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 gap-1">
                             @if(!empty($empresa->item_lc_id))
                                <span id="nbs_container">
                                    <label class="block text-sm">
                                        <span class="text-gray-700 ">NBS:</span>
                                        {!! Form::select('nbs_id', $nbs_list
                                        ,$nota_original['nbs_id'] ?? $empresa->nbs_id, [empty($nbs_list) ? '' : 'required','class'=>'block w-full mt-1 text-sm  
                                        
                                        form-select
                                        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray', 'id' => 'nbs_id']) !!}
                                        @if ($errors->has('nbs_id'))
                                            <span class="text-xs text-red-600 ">
                                            <strong>{{ $errors->first('nbs_id') }}</strong>
                                        </span>
                                        @endif
                                    </label>
                                </span>
                            @endif  
                        </div>

                        <div class="grid grid-cols-1 gap-1">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Descrição dos Serviços(*) - Caracteres Restantes:</span>
                                <span id="LblLines" class="aspLabel">2000</span>
                                {!!
                                Form::textarea('txtDescServicos', $nota_original['txtDescServicos'] ?? null, [
                                    'name'=>"txtDescServicos",
                                    'id'=>"txtDescServicos",
                                    'style'=>"height: 100px !important;",
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



                        <div class="grid md:grid-cols-5 gap-1" style="{{$isMei}}">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Alíquota.%:</span>
                                {!! Form::text('txtAliq', !isset($aliquotaAtividade)? null : $aliquotaAtividade, [(!$empresa->is_mei ? 'required' : '' ),'maxlength' => '5','class'=>'block w-full mt-1 text-sm  
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                                :shadow-outline-gray form-input',
                                'placeholder'=>'0,00',
                                'onkeypress'=>"SoNumeros(event); FormataMoeda(this.name,event);",
                                'id'=>'txtAliq',
                                'name'=>"txtAliq",
                                ]) !!}
                                @if ($errors->has('txtAliq'))
                                    <span class="text-xs text-red-600 ">
                                        <strong>{{ $errors->first('txtAliq') }}</strong>
                                    </span>
                                @endif
                            </label>
                        </div>

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
                                    'onblur'=>"ImpostoRetido('chkIssqnRetido','txtTotal','txtAliq','txtISSQNResponsavel','txtTotalISSQN','txtDeducaoBaseCalculo','txtDescontoInCondicionado');SumFields(['txtPis','txtCofins','txtCSLL','txtIRRF','txtOutros','txtOutrasRetencoes','txtISSQNResponsavel'],'txtTotalRetencoes');CalculaValorLiquido(['txtTotalRetencoes','txtValorDescontos'],['txtTotal'],'txtTotalValorLiquido');"
                             ])
                  !!}
                                @if ($errors->has('txtTotal'))
                                    <span class="text-xs text-red-600 ">
                                        <strong>{{ $errors->first('txtTotal') }}</strong>
                                    </span>
                                @endif
                            </label>

                            <label class="block text-sm" style="{{$isMei}} {{ $permiteDescontoCond }}">
                                <span class="text-gray-700 ">Desconto Condic. *:</span>
                                <input name="txtDescontoCondicionado" type="text" maxlength="20" placeholder="0,00" id="txtDescontoCondicionado" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;" onblur="ImpostoRetido('chkIssqnRetido','txtTotal','txtAliq','txtISSQNResponsavel','txtTotalISSQN','txtDeducaoBaseCalculo','txtDescontoInCondicionado'); SumFields(['txtDescontoCondicionado','txtDescontoInCondicionado'],'txtValorDescontos'); CalculaValorLiquido(['txtTotalRetencoes','txtValorDescontos'],['txtTotal'],'txtTotalValorLiquido');">
                            </label>

                            <label class="block text-sm" style="{{$isMei}} {{ $permiteDescontoInc  }}">
                                <span class="text-gray-700 ">Desconto Incondic. *:</span>
                                <input name="txtDescontoInCondicionado" type="text" maxlength="20" id="txtDescontoInCondicionado" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;" onblur="ImpostoRetido('chkIssqnRetido','txtTotal','txtAliq','txtISSQNResponsavel','txtTotalISSQN','txtDeducaoBaseCalculo','txtDescontoInCondicionado'); SumFields(['txtDescontoCondicionado','txtDescontoInCondicionado'],'txtValorDescontos'); CalculaValorLiquido(['txtTotalRetencoes','txtValorDescontos'],['txtTotal'],'txtTotalValorLiquido');">
                            </label>

                            <label class="block text-sm" style="{{$isMei}} {{ $permiteDeducao }}">
                                <span class="text-gray-700 ">Deduções Base Cálc. *:</span>
                                <input name="txtDeducaoBaseCalculo" type="text" maxlength="20" id="txtDeducaoBaseCalculo" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;" onblur="ImpostoRetido('chkIssqnRetido','txtTotal','txtAliq','txtISSQNResponsavel','txtTotalISSQN','txtDeducaoBaseCalculo','txtDescontoInCondicionado');SumFields(['txtPis','txtCofins','txtCSLL','txtIRRF','txtOutros','txtOutrasRetencoes','txtISSQNResponsavel'],'txtTotalRetencoes');CalculaValorLiquido(['txtTotalRetencoes','txtValorDescontos'],['txtTotal'],'txtTotalValorLiquido');">
                            </label>

                            <label class="block text-sm" style="{{$isMei}}">
                                <span class="text-gray-700 ">Total do ISSQN:</span>
                                <input style="background: #e9ecef;" name="txtTotalISSQN" type="text" maxlength="20" id="txtTotalISSQN" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" readonly="readonly">
                            </label>

                            <label class="block text-sm" style="{{$isMei}}">
                                <span class="text-gray-700 ">ISSQN Retido:</span>
                                <input id="chkIssqnRetido" style="margin-top: 8px;" type="checkbox" name="chkIssqnRetido"
                                       onclick="ImpostoRetido('chkIssqnRetido', 'txtTotal', 'txtAliq', 'txtISSQNResponsavel', 'txtTotalISSQN', 'txtDeducaoBaseCalculo', 'txtDescontoInCondicionado');
        SumFields(['txtPis','txtCofins','txtCSLL', 'txtIRRF','txtOutros','txtOutrasRetencoes','txtISSQNResponsavel'],'txtTotalRetencoes');
        CalculaValorLiquido(['txtTotalRetencoes','txtValorDescontos'],['txtTotal'],'txtTotalValorLiquido');">
                            </label>
                        </div>

                        <h4 class="mb-4 mt-4 text-lg font-semibold text-gray-600 " style="{{$isMei}}">
                            Retenções
                        </h4>
                        <div style="background-color: #F8F8F8;{{$isMei}}" class="grid md:grid-cols-5 gap-1 p-4">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">PIS:</span>
                                <input name="txtPis" type="text" value="{{ isset($nota_original) && $nota_original['txtPis'] > 0 ? $nota_original['txtPis'] : null }}" maxlength="20" id="txtPis" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="Money2CasasDecimais(this,event);"
                                       onblur="ImpostoRetido('chkIssqnRetido','txtTotal','txtAliq','txtISSQNResponsavel','txtTotalISSQN','txtDeducaoBaseCalculo','txtDescontoInCondicionado');SumFields(['txtPis','txtCofins','txtCSLL','txtIRRF','txtOutros','txtOutrasRetencoes','txtISSQNResponsavel'],'txtTotalRetencoes'); CalculaValorLiquido(['txtTotalRetencoes','txtValorDescontos'],['txtTotal'],'txtTotalValorLiquido');">
                            </label>
                            <label class="block text-sm">
                                <span class="text-gray-700 ">COFINS:</span>
                                <input name="txtCofins" type="text" value="{{ isset($nota_original) && $nota_original['txtCofins'] > 0 ? $nota_original['txtCofins'] : null }}" maxlength="20" id="txtCofins" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="Money2CasasDecimais(this,event);" onblur="ImpostoRetido('chkIssqnRetido','txtTotal','txtAliq','txtISSQNResponsavel','txtTotalISSQN','txtDeducaoBaseCalculo','txtDescontoInCondicionado');SumFields(['txtPis','txtCofins','txtCSLL','txtIRRF','txtOutros','txtOutrasRetencoes','txtISSQNResponsavel'],'txtTotalRetencoes'); CalculaValorLiquido(['txtTotalRetencoes','txtValorDescontos'],['txtTotal'],'txtTotalValorLiquido');">
                            </label>
                            <label class="block text-sm">
                                <span class="text-gray-700 ">INSS:</span>
                                <input name="txtOutros" type="text" value="{{ isset($nota_original) && $nota_original['txtOutros'] > 0 ? $nota_original['txtOutros'] : null }}"  maxlength="20" id="txtOutros" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="Money2CasasDecimais(this,event);" onblur="ImpostoRetido('chkIssqnRetido','txtTotal','txtAliq','txtISSQNResponsavel','txtTotalISSQN','txtDeducaoBaseCalculo','txtDescontoInCondicionado');SumFields(['txtPis','txtCofins','txtCSLL','txtIRRF','txtOutros','txtOutrasRetencoes','txtISSQNResponsavel'],'txtTotalRetencoes'); CalculaValorLiquido(['txtTotalRetencoes','txtValorDescontos'],['txtTotal'],'txtTotalValorLiquido');">
                            </label>
                            <label class="block text-sm">
                                <span class="text-gray-700 ">IRRF:</span>
                                <input name="txtIRRF" type="text" value="{{ isset($nota_original) && $nota_original['txtIRRF'] > 0 ? $nota_original['txtIRRF'] : null }}" maxlength="20"  id="txtIRRF" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="Money2CasasDecimais(this,event);" onblur="ImpostoRetido('chkIssqnRetido','txtTotal','txtAliq','txtISSQNResponsavel','txtTotalISSQN','txtDeducaoBaseCalculo','txtDescontoInCondicionado');SumFields(['txtPis','txtCofins','txtCSLL','txtIRRF','txtOutros','txtOutrasRetencoes','txtISSQNResponsavel'],'txtTotalRetencoes'); CalculaValorLiquido(['txtTotalRetencoes','txtValorDescontos'],['txtTotal'],'txtTotalValorLiquido');">
                            </label>
                            <label class="block text-sm">
                                <span class="text-gray-700 ">CSLL:</span>
                                <input name="txtCSLL" type="text" value="{{ isset($nota_original) && $nota_original['txtCSLL'] > 0 ? $nota_original['txtCSLL'] : null }}" maxlength="20" id="txtCSLL" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="Money2CasasDecimais(this,event);" onblur="ImpostoRetido('chkIssqnRetido','txtTotal','txtAliq','txtISSQNResponsavel','txtTotalISSQN','txtDeducaoBaseCalculo','txtDescontoInCondicionado');SumFields(['txtPis','txtCofins','txtCSLL','txtIRRF','txtOutros','txtOutrasRetencoes','txtISSQNResponsavel'],'txtTotalRetencoes'); CalculaValorLiquido(['txtTotalRetencoes','txtValorDescontos'],['txtTotal'],'txtTotalValorLiquido');">
                            </label>
                            <label class="block text-sm">
                                <span class="text-gray-700 ">ISSQN Retido:</span>
                                <input style="background: #e9ecef;" name="txtISSQNResponsavel" type="text" maxlength="20" id="txtISSQNResponsavel" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" readonly="readonly">
                            </label>
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Outras Retenções:</span>
                                <input name="txtOutrasRetencoes" value="{{ isset($nota_original) && $nota_original['txtOutrasRetencoes'] > 0 ? $nota_original['txtOutrasRetencoes'] : null }}" type="text" maxlength="20" id="txtOutrasRetencoes" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="Money2CasasDecimais(this,event);" onpaste="return false;" onblur="ImpostoRetido('chkIssqnRetido','txtTotal','txtAliq','txtISSQNResponsavel','txtTotalISSQN','txtDeducaoBaseCalculo','txtDescontoInCondicionado');SumFields(['txtPis','txtCofins','txtCSLL','txtIRRF','txtOutros','txtOutrasRetencoes','txtISSQNResponsavel'],'txtTotalRetencoes'); CalculaValorLiquido(['txtTotalRetencoes','txtValorDescontos'],['txtTotal'],'txtTotalValorLiquido');">
                            </label>
                        </div>

                        <h4 class="mb-4 mt-4 text-lg font-semibold text-gray-600 ">
                            Resumo da Nota
                        </h4>


                        <div class="grid md:grid-cols-4 gap-1">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Valor Total dos Serviços(*):</span>
                                {!! Form::text('txtTotal2', null, ['required', 'maxlength' => 15,
                                        'name'=>'txtTotal2',
                                        'id' => 'txtTotal2',
                                        'class'=>'block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input',
                                        'placeholder'=> "0,00",
                                        'onkeypress'=>'SoNumeros(event);FormataMoeda(this.name,event);',
                                        'onpaste'=> 'return false;',
                                        'style'=>'background: #e9ecef;',
										'readonly' => 'readonly'
                                    ]) !!}
                            </label>
                            <label class="block text-sm" style="{{$isMei}}">
                                <span class="text-gray-700 ">Valor Descontos:</span>
                                <input  style="background: #e9ecef;" 
                                name="txtValorDescontos" 
                                placeholder="0,00" 
                                type="text" 
                                maxlength="15" 
                                id="txtValorDescontos" 
                                class="block w-full mt-1 text-sm  
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                                :shadow-outline-gray form-input" 
                                readonly="readonly">
                            </label>
                            <label class="block text-sm" style="{{$isMei}}">
                                <span class="text-gray-700 ">Valor Retenções:</span>
                                <input  style="background: #e9ecef;" name="txtTotalRetencoes" placeholder="0,00" type="text" maxlength="15" id="txtTotalRetencoes" 
                                class="block w-full mt-1 text-sm  
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                                :shadow-outline-gray form-input" readonly="readonly">
                            </label>
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Valor Líquido:</span>
                                <input style="background: #e9ecef;" name="txtTotalValorLiquido" placeholder="0,00" type="text" value="0,00" maxlength="50" id="txtTotalValorLiquido" 
                                class="block w-full mt-1 text-sm  
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                                :shadow-outline-gray form-input" readonly="readonly">
                                 @if ($errors->has('txtTotalValorLiquido'))
                                    <span class="text-xs text-red-600 ">
                                        <strong>{{ $errors->first('txtTotalValorLiquido') }}</strong>
                                    </span>
                                @endif
                            </label>
                        </div>

                        <div class="grid grid-cols-1 gap-1">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Informações Complementares(*) - Caracteres Restantes:</span>
                                <span id="LblLines2" class="aspLabel">2000</span>
                                {!!
                                Form::textarea('txtInfoComplementares', $nota_original['txtInfoComplementares'] ?? '.', [
                                    'name'=>"txtInfoComplementares",
                                    'id'=>"txtInfoComplementares",
                                    'style'=>"height: 100px !important;",
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



