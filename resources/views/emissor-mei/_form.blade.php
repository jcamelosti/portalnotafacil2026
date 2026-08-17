        {{-- Tomador do Serviço --}}
        <div class="bg-white shadow rounded-lg p-4 space-y-4">
            <h2 class="text-green-700 font-semibold text-sm uppercase">
                Dados do Tomador do Serviço
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-gray-600">Cliente *</label>
                    @if (!request()->routeIs('servicos-mei.editar'))
                        {!! Form::select('tomador_id', []
                        ,null, ['required','id'=>'tomador_select','class'=>'w-full border rounded-lg px-3 py-2 text-sm']) !!}
                        @if ($errors->has('tomador_id'))
                        <span class="text-xs text-red-600 ">
                            <strong>{{ $errors->first('tomador_id') }}</strong>
                        </span>
                        @endif
                    @else
                        @if(!is_null($servico->tomador_id))
                        {!! Form::select('tomador_id', $tomadoresList
                        ,null, ['required','id'=>'tomador_id','class'=>'select2 w-full border rounded-lg px-3 py-2 text-sm']) !!}
                        @if ($errors->has('tomador_id'))
                            <span class="text-xs text-red-600 ">
                            <strong>{{ $errors->first('tomador_id') }}</strong>
                        </span>
                        @endif
                        @else
                        {!! Form::select('empresa_cliente_id', $empresasList
                        ,null, ['required','id'=>'empresa_cliente_id','class'=>'select2 w-full border rounded-lg px-3 py-2 text-sm']) !!}
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- LOCAL DA PRESTAÇÃO --}}
        <div class="bg-white shadow rounded-lg p-4 space-y-4">
            <h2 class="text-green-700 font-semibold text-sm uppercase">
                Local da Prestação do Serviço
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!--div>
                    <label class="text-xs text-gray-600">País *</label>
                    <select id="pais" class="w-full border rounded-lg px-3 py-2 text-sm"></select>
                </div-->
                <div>
                    <label class="text-xs text-gray-600">Município *</label>
                    @if (!request()->routeIs('servicos-mei.editar'))
                    {!! Form::select('local_prestacao', $municipios ?? []
                    ,null, ['required','id'=>'local_prestacao_servico_select','class'=>'w-full border rounded-lg px-3 py-2 text-sm']) !!}
                    @else
                        {!! Form::select('local_prestacao', $municipios ?? []
                        ,null, ['required','id'=>'local_prestacao_servico_select','class'=>'select2 w-full border rounded-lg px-3 py-2 text-sm']) !!}
                    @endif
                    @if ($errors->has('local_prestacao'))
                        <span class="text-xs text-red-600 ">
                        <strong>{{ $errors->first('local_prestacao') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- SERVIÇO --}}
        <div class="bg-white shadow rounded-lg p-4 space-y-4">

            <h2 class="text-green-700 font-semibold text-sm uppercase">
                Serviço Prestado
            </h2>

            <div>
                <label class="text-xs text-gray-600">
                    Código de Tributação Nacional *
                </label>

                @if (!request()->routeIs('servicos-mei.editar'))
                {!! Form::select('cod_trib_nacional_id', isset($cod_trib_nac) ? $cod_trib_nac : []
                ,null, ['required','id'=>'cod_trib_nacional_id','class'=>'w-full border rounded-lg px-3 py-2 text-sm']) !!}
                @else
                {!! Form::select('cod_trib_nacional_id', isset($cod_trib_nac) ? $cod_trib_nac : []
                ,null, ['required','id'=>'cod_trib_nacional_id','class'=>'select2 w-full border rounded-lg px-3 py-2 text-sm']) !!}
                @endif
                @if ($errors->has('cod_trib_nacional_id'))
                    <span class="text-xs text-red-600 ">
                    <strong>{{ $errors->first('cod_trib_nacional_id') }}</strong>
                </span>
                @endif
            </div>

            <!--div>
                <label class="text-xs text-gray-600">
                    Possui imunidade, exportação ou não incidência de ISSQN *
                </label>

                <select id="incidencia"
                class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="0">Não</option>
                    <option value="1">Sim</option>
                </select>
            </div-->

            <!--div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="text-xs text-gray-600">
                        Município de incidência do ISSQN
                    </label>

                    <select id="municipio_iss"
                    class="w-full border rounded-lg px-3 py-2 text-sm"></select>
                </div>

                <div>
                    <label class="text-xs text-gray-600">
                        Data de Competência
                    </label>

                    <input type="date"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

            </div-->

            <div>
                <!--label class="text-xs text-gray-600">
                    Descrição do Serviço *
                </label-->
                <span class="text-gray-700 ">Descrição dos Serviços(*) - Caracteres Restantes:</span>
                <span id="LblLines" class="aspLabel">2000</span>
                
                {!!
                Form::textarea('descricao', $servico->descricao ?? null, [
                    'name'=>"descricao",
                    'id'=>"descricao",
                    //'style'=>"height: 100px !important;",
                    'onblur'=>"ReplaceMaxLenght(this, 2000);",
                    'onkeydown'=>"qtd_caracCont('servico', 'LblLines',2000, event)",
                    'maxlength'=>"2000",
                    'required',
                    'class'=>'w-full border rounded-lg px-3 py-2 text-sm',
                    'placeholder'=>'Descrição do Serviço - Este Campo é Obrigatório',
                ]) !!}
                @if ($errors->has('descricao'))
                    <span class="text-xs text-red-600 ">
                    <strong>{{ $errors->first('descricao') }}</strong>
                </span>
                @endif
            </div>

            <div>
                <label class="text-xs text-gray-600">
                    Item da NBS correspondente ao serviço prestado *
                </label>

                @if (!request()->routeIs('servicos-mei.editar'))
                {!! Form::select('nbs_id', isset($nbs) ? $nbs : []
                ,null, ['required','id'=>'nbs_id','class'=>'w-full border rounded-lg px-3 py-2 text-sm']) !!}
                @else
                {!! Form::select('nbs_id', isset($nbs) ? $nbs : []
                ,null, ['required','id'=>'nbs_id','class'=>'select2 w-full border rounded-lg px-3 py-2 text-sm']) !!}
                @endif
                @if ($errors->has('nbs_id'))
                    <span class="text-xs text-red-600 ">
                    <strong>{{ $errors->first('nbs_id') }}</strong>
                </span>
                @endif
            </div>
        </div>


        {{-- VALORES --}}
        <div class="bg-white shadow rounded-lg p-4 space-y-4">

            <h2 class="text-green-700 font-semibold text-sm uppercase">
                Valores do Serviço Prestado
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div>
                    <label class="text-xs text-gray-600">
                        Valor do serviço prestado *
                    </label>

                    {!! Form::text('valor_servico', $servico->valor_servico_fmt ?? null, [
                        'placeholder'=> "0,00",
                        'name'=>'valor_servico',
                        'type'=>"text", 'maxlength'=>"50", 'id'=>"valor_servico", 'class'=>"w-full border rounded-lg px-3 py-2 text-sm",
                        'onkeypress'=>"SoNumeros(event); FormataMoeda(this.name,event);",
                        'onpaste'=>"return false;",
                        'onblur'=>"" ])!!}
                    @if ($errors->has('valor_servico'))
                        <span class="text-xs text-red-600 ">
                        <strong>{{ $errors->first('valor_servico') }}</strong>
                    </span>
                @endif
                </div>

                <!--div>
                    <label class="text-xs text-gray-600">
                        Valor recebido pelo intermediário
                    </label>

                    <input type="number" step="0.01"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="text-xs text-gray-600">
                        Desconto incondicionado
                    </label>

                    <input type="number" step="0.01"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="text-xs text-gray-600">
                        Desconto condicionado
                    </label>

                    <input type="number" step="0.01"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
                </div-->

            </div>

        </div>