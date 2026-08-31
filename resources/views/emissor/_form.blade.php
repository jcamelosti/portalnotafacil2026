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

                        <h4 class="mb-4 mt-4 text-base font-semibold text-white bg-gray-500 py-4 px-0 rounded-md">
                            Tomador do Serviço: {{ $tomador->razao_social}} - {{ $tomador->cpf_cnpj}}
                        </h4>
                        
                         <h4 class="mb-4 mt-4 text-base font-semibold text-white bg-gray-500 py-4 px-0 rounded-md">
                            Identificação dos Serviços
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
                                ,old('empresa_atividade_id'), ['required','class'=>'block w-full mt-1 text-sm  
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
                        
                        <div class="grid md:grid-cols-5 gap-1">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Valor Total dos Serviços(*):</span>
                                        {!! Form::text('txtTotal', isset($nota_original) ? number_format($nota_original['txtTotal'],2,',','.') : null, [
                                            'placeholder'=> "0,00",
                                            'name'=>'txtTotal',
                                            'required',
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

                        <h4 class="mb-4 mt-4 text-base font-semibold text-white bg-gray-500 py-4 px-0 rounded-md">
                            Local da Prestação do Serviço
                        </h4>

                        <div class="grid md:grid-cols-4 gap-1 mt-4 mb-4">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Pais*:</span>
                                <select name="ddlPaisPrestacao" onchange="" required language="javascript" id="ddlPaisPrestacao" 
                                    class="block w-full mt-1 text-sm px-3 py-1.5 form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray">
                                        	<option selected="selected" value="26">Brasil</option>
                                            <option value="192">Afeganistão</option>
                                            <option value="2">África do Sul</option>
                                            <option value="196">Aland, Ilhas</option>
                                            <option value="3">Albânia, República da</option>
                                            <option value="4">Alemanha</option>
                                            <option value="5">Andorra</option>
                                            <option value="6">Angola</option>
                                            <option value="197">Anguilla</option>
                                            <option value="198">Antártica</option>
                                            <option value="7">Antígua e Barbuda</option>
                                            <option value="8">Arábia Saudita</option>
                                            <option value="9">Argélia</option>
                                            <option value="10">Argentina</option>
                                            <option value="11">Armênia, República da</option>
                                            <option value="200">Aruba</option>
                                            <option value="12">Austrália</option>
                                            <option value="13">Áustria</option>
                                            <option value="14">Azerbaijão, República do</option>
                                            <option value="15">Bahamas, Ilhas</option>
                                            <option value="16">Bahrein, Ilhas</option>
                                            <option value="17">Bangladesh</option>
                                            <option value="18">Barbados</option>
                                            <option value="201">Belarus, República da</option>
                                            <option value="19">Bélgica</option>
                                            <option value="20">Belize</option>
                                            <option value="21">Benim</option>
                                            <option value="202">Bermudas</option>
                                            <option value="23">Bolívia</option>
                                            <option value="203">Bonaire, Santo Eustáquio e Saba</option>
                                            <option value="24">Bosnia-Herzegovina, República da</option>
                                            <option value="25">Botsuana</option>
                                            <option value="205">Bouvet, Ilha</option>
                                            <option value="27">Brunei</option>
                                            <option value="28">Bulgária, República da</option>
                                            <option value="29">Burkina Faso</option>
                                            <option value="30">Burundi</option>
                                            <option value="31">Butão</option>
                                            <option value="32">Cabo Verde, República de</option>
                                            <option value="33">Camarões</option>
                                            <option value="34">Camboja</option>
                                            <option value="35">Canadá</option>
                                            <option value="36">Catar</option>
                                            <option value="207">Cayman, Ilhas</option>
                                            <option value="37">Cazaquistão, República do</option>
                                            <option value="38">Chade</option>
                                            <option value="39">Chile</option>
                                            <option value="40">China, República Popular</option>
                                            <option value="41">Chipre</option>
                                            <option value="208">Christmas, Ilha</option>
                                            <option value="42">Cingapura</option>
                                            <option value="209">Cocos(Keeling), Ilhas</option>
                                            <option value="43">Colômbia</option>
                                            <option value="86">Comores, Ilhas</option>
                                            <option value="44">Congo</option>
                                            <option value="210">Congo, República Democrática do</option>
                                            <option value="211">Cook, Ilhas</option>
                                            <option value="45">Coréia (do Norte), Rep. Pop. Democrática da</option>
                                            <option value="46">Coréia (do Sul), República da</option>
                                            <option value="47">Costa do Marfim</option>
                                            <option value="48">Costa Rica</option>
                                            <option value="49">Croácia, República da</option>
                                            <option value="50">Cuba</option>
                                            <option value="213">Curaçao</option>
                                            <option value="51">Dinamarca</option>
                                            <option value="52">Djibuti</option>
                                            <option value="53">Dominica, Ilha</option>
                                            <option value="54">Egito</option>
                                            <option value="55">El Salvador</option>
                                            <option value="56">Emirados Árabes Unidos</option>
                                            <option value="57">Equador</option>
                                            <option value="58">Eritreia</option>
                                            <option value="59">Eslovaca, República</option>
                                            <option value="60">Eslovênia, República da</option>
                                            <option value="61">Espanha</option>
                                            <option value="62">Estados Unidos</option>
                                            <option value="63">Estônia, República da</option>
                                            <option value="64">Etiópia</option>
                                            <option value="214">Falkland (Ilhas Malvinas)</option>
                                            <option value="215">Feroe, Ilhas</option>
                                            <option value="65">Fiji</option>
                                            <option value="66">Filipinas</option>
                                            <option value="67">Finlândia</option>
                                            <option value="68">Formosa (Taiwan)</option>
                                            <option value="69">França</option>
                                            <option value="70">Gabão</option>
                                            <option value="71">Gambia</option>
                                            <option value="72">Gana</option>
                                            <option value="73">Geórgia, República da</option>
                                            <option value="216">Gibraltar</option>
                                            <option value="74">Granada</option>
                                            <option value="75">Grécia</option>
                                            <option value="217">Groenlândia</option>
                                            <option value="218">Guadalupe</option>
                                            <option value="219">Guam</option>
                                            <option value="76">Guatemala</option>
                                            <option value="221">Guernsey</option>
                                            <option value="77">Guiana</option>
                                            <option value="222">Guiana Francesa</option>
                                            <option value="78">Guiné</option>
                                            <option value="80">Guiné-Bissau</option>
                                            <option value="79">Guiné-Equatorial</option>
                                            <option value="81">Haiti</option>
                                            <option value="83">Honduras</option>
                                            <option value="223">Hong Kong</option>
                                            <option value="84">Hungria, República da</option>
                                            <option value="85">Iêmen</option>
                                            <option value="224">Ilha Heard e Ilhas McDonald</option>
                                            <option value="225">Ilhas Geórgia do Sul e Sandwich do Sul</option>
                                            <option value="88">Índia</option>
                                            <option value="89">Indonésia</option>
                                            <option value="90">Irã, República Islâmica do</option>
                                            <option value="91">Iraque</option>
                                            <option value="92">Irlanda</option>
                                            <option value="93">Islândia</option>
                                            <option value="94">Israel</option>
                                            <option value="95">Itália</option>
                                            <option value="97">Jamaica</option>
                                            <option value="98">Japão</option>
                                            <option value="227">Jersey</option>
                                            <option value="99">Jordânia</option>
                                            <option value="229">Kiribati</option>
                                            <option value="100">Kuwait</option>
                                            <option value="101">Laos, Rep. Pop. Democrática do</option>
                                            <option value="102">Lesoto</option>
                                            <option value="103">Letônia, República da</option>
                                            <option value="104">Líbano</option>
                                            <option value="105">Libéria</option>
                                            <option value="106">Líbia</option>
                                            <option value="107">Liechtenstein</option>
                                            <option value="108">Lituânia, República da</option>
                                            <option value="109">Luxemburgo</option>
                                            <option value="231">Macau</option>
                                            <option value="110">Macedônia, Ant. Rep. Iugoslava</option>
                                            <option value="111">Madagascar</option>
                                            <option value="112">Malásia</option>
                                            <option value="113">Malavi</option>
                                            <option value="114">Maldivas</option>
                                            <option value="115">Mali</option>
                                            <option value="116">Malta</option>
                                            <option value="233">Man, Ilha de</option>
                                            <option value="234">Marianas do Norte</option>
                                            <option value="117">Marrocos</option>
                                            <option value="118">Marshall, Ilhas</option>
                                            <option value="235">Martinica</option>
                                            <option value="119">Maurício</option>
                                            <option value="120">Mauritânia</option>
                                            <option value="237">Mayotte</option>
                                            <option value="121">México</option>
                                            <option value="123">Micronésia</option>
                                            <option value="124">Moçambique</option>
                                            <option value="125">Moldávia, República da</option>
                                            <option value="126">Mônaco</option>
                                            <option value="127">Mongólia</option>
                                            <option value="239">Montenegro</option>
                                            <option value="240">Montserrat, Ilhas</option>
                                            <option value="122">Myanmar (Birmânia)</option>
                                            <option value="128">Namíbia</option>
                                            <option value="129">Nauru</option>
                                            <option value="130">Nepal</option>
                                            <option value="131">Nicarágua</option>
                                            <option value="132">Níger</option>
                                            <option value="133">Nigéria</option>
                                            <option value="241">Niue, Ilha</option>
                                            <option value="242">Norfolk, Ilha</option>
                                            <option value="134">Noruega</option>
                                            <option value="243">Nova Caledônia</option>
                                            <option value="135">Nova Zelândia</option>
                                            <option value="136">Omã</option>
                                            <option value="244">Pacífico, Ilhas do (Possessão dos EUA)</option>
                                            <option value="82">Países Baixos (Holanda)</option>
                                            <option value="137">Palau</option>
                                            <option value="245">Palestina</option>
                                            <option value="138">Panamá</option>
                                            <option value="139">Papua Nova Guiné</option>
                                            <option value="140">Paquistão</option>
                                            <option value="141">Paraguai</option>
                                            <option value="142">Peru</option>
                                            <option value="246">Pitcairn, Ilha De</option>
                                            <option value="247">Polinésia Francesa</option>
                                            <option value="143">Polônia, República da</option>
                                            <option value="248">Porto Rico</option>
                                            <option value="144">Portugal</option>
                                            <option value="145">Quênia</option>
                                            <option value="249">Quirguiz, República da</option>
                                            <option value="148">Reino Unido</option>
                                            <option value="149">República Centro-Africana</option>
                                            <option value="150">República Dominicana</option>
                                            <option value="250">Reunião, Ilha</option>
                                            <option value="152">Romênia</option>
                                            <option value="153">Ruanda</option>
                                            <option value="154">Rússia, Federação da</option>
                                            <option value="251">Saara Ocidental</option>
                                            <option value="87">Salomão, Ilhas</option>
                                            <option value="252">Samoa</option>
                                            <option value="253">Samoa Americana</option>
                                            <option value="156">San Marino</option>
                                            <option value="254">Santa Helena</option>
                                            <option value="157">Santa Lúcia</option>
                                            <option value="255">São Bartolomeu</option>
                                            <option value="256">São Cristóvão e Neves, Ilhas</option>
                                            <option value="257">São Martinho (Parte Francesa)</option>
                                            <option value="258">São Martinho (Parte Holandesa)</option>
                                            <option value="259">São Pedro e Miquelon</option>
                                            <option value="159">São Tomé e Príncipe, Ilhas</option>
                                            <option value="160">São Vicente e Granadinas</option>
                                            <option value="162">Senegal</option>
                                            <option value="163">Serra Leoa</option>
                                            <option value="260">Servia</option>
                                            <option value="161">Seychelles</option>
                                            <option value="164">Síria, República Árabe da</option>
                                            <option value="165">Somália</option>
                                            <option value="166">Sri Lanka</option>
                                            <option value="167">Suazilândia</option>
                                            <option value="168">Sudão</option>
                                            <option value="261">Sudão do Sul</option>
                                            <option value="169">Suécia</option>
                                            <option value="170">Suíça</option>
                                            <option value="171">Suriname</option>
                                            <option value="262">Svalbard e Jan Mayen</option>
                                            <option value="172">Tadjiquistão, República do</option>
                                            <option value="173">Tailândia</option>
                                            <option value="174">Tanzânia, Rep. Unida da</option>
                                            <option value="151">Tcheca, República</option>
                                            <option value="263">Terras Austrais e Antárticas Francesas</option>
                                            <option value="264">Território Britânico no Oceano Índico</option>
                                            <option value="265">Timor Leste</option>
                                            <option value="175">Togo</option>
                                            <option value="176">Tonga</option>
                                            <option value="266">Toquelau, Ilhas</option>
                                            <option value="177">Trinidad e Tobago</option>
                                            <option value="178">Tunísia</option>
                                            <option value="267">Turcas e Caicos, Ilhas</option>
                                            <option value="180">Turcomenistão, República do</option>
                                            <option value="179">Turquia</option>
                                            <option value="181">Tuvalu</option>
                                            <option value="182">Ucrânia</option>
                                            <option value="268">Uganda</option>
                                            <option value="183">Uruguai</option>
                                            <option value="184">Uzbequistão, República do</option>
                                            <option value="185">Vanuatu</option>
                                            <option value="186">Vaticano, Est. da Cidade do</option>
                                            <option value="187">Venezuela</option>
                                            <option value="188">Vietnã</option>
                                            <option value="269">Virgens, Ilhas (Britânicas)</option>
                                            <option value="270">Virgens, Ilhas (E.U.A.)</option>
                                            <option value="272">Wallis e Futuna, Ilhas</option>
                                            <option value="189">Zâmbia</option>
                                            <option value="190">Zimbábue</option>
                                    </select>
                                    @if ($errors->has('nbs'))
                                        <span class="text-xs text-red-600 ">
                                        <strong>{{ $errors->first('nbs') }}</strong>
                                    </span>
                                    @endif
                            </label>

                            <label class="block text-sm">
                                <span class="text-gray-700 ">UF*:</span>
                                {!! Form::select('ddlEstadoPrestacao',
                                $estados
                                ,$uf_id, ['id' => 'ddlEstadoPrestacao','required','class'=>'block w-full mt-1 text-sm  
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                @if ($errors->has('ddlEstadoPrestacao'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('ddlEstadoPrestacao') }}</strong>
                                </span>
                                @endif
                            </label>

                             <label class="block text-sm w-10/12" id="cidade_id">
                                <span class="text-gray-700 ">Cidade*:</span>
                                {!! Form::select('ddlCidadePrestacao', $cidades, $empresa->cidade_id, [
                                    'id' => 'ddlCidadePrestacao', 
                                    'maxlength' => 
                                    '255',
                                    'required',
                                    'class'=>'block w-full mt-1 text-sm  
                                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                                    :shadow-outline-gray form-input', 'placeholder'=>'']) !!}
                                @if ($errors->has('ddlCidadePrestacao'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('ddlCidadePrestacao') }}</strong>
                                </span>
                                @endif
                            </label>
                        </div>

                        <h4 class="mb-4 mt-4 text-base font-semibold text-white bg-gray-500 py-4 px-0 rounded-md">
                            Regimes de Tributação do Prestador de Serviço
                        </h4>

                        <div class="grid md:grid-cols-2 gap-1 mt-4 mb-4">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Situação Perante Simples Nacional*</span>
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
                                    <span class="text-gray-700 ">Regime de Apuração Tributária pelo Simples Nacional*:</span>
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
                        </div>

                        <h4 class="mb-4 mt-4 text-base font-semibold text-white bg-gray-500 py-4 px-0 rounded-md">
                            Impostos Sobre Serviços de Qualquer Natureza - ISSQN
                        </h4>
                        <div class="grid md:grid-cols-4 gap-1 mt-4 mb-4">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Tributação do ISSQN*</span>
                                {!! Form::select('ddlTribISSQN', $tributacao_issqn_list
                                ,null, ['required','id' => 'ddlTribISSQN','class'=>'block w-full mt-1 text-sm  
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                @if ($errors->has('ddlTribISSQN'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{ { $errors->first('ddlTribISSQN') }}</strong>
                                </span>
                                @endif
                            </label>

                            <label class="block text-sm">
                                <span class="text-gray-700 ">Tipo Imunidade</span>
                                {!! Form::select('ddlImunidade', []
                                ,null, ['id' => 'ddlImunidade','class'=>'block w-full mt-1 text-sm  
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                @if ($errors->has('ddlImunidade'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{ { $errors->first('ddlImunidade') }}</strong>
                                </span>
                                @endif
                            </label>

                            <label class="block text-sm">
                                <span class="text-gray-700 ">Tipo de Suspensão Exigibilidade</span>
                                {!! Form::select('ddlSuspExig', []
                                ,null, ['id'=> 'ddlSuspExig' ,'class'=>'block w-full mt-1 text-sm  
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                @if ($errors->has('ddlSuspExig'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{ { $errors->first('ddlSuspExig') }}</strong>
                                </span>
                                @endif
                            </label>

                            <label class="block text-sm">
                                <span class="text-gray-700 ">Nº Processo Suspensão Exigibilidade. *:</span>
                                <input name="txtProcExig" type="text" id="txtProcExig" placeholder="" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="" onpaste="return false;" onblur="">
                                @if ($errors->has('txtProcExig'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{ { $errors->first('txtProcExig') }}</strong>
                                </span>
                                @endif
                            </label>
                        </div>

                        <div class="grid md:grid-cols-2 gap-1 mt-4 mb-4">
                            <label class="block text-sm w-10/12" id="cidade_incidencia_id">
                                <span class="text-gray-700 ">Município Incidência (Cidade/UF)</span>
                                {!! Form::select('ddlMunInci', $cidades, $empresa->cidade_id, [
                                    'id' => 'ddlMunInci', 
                                    'maxlength' => '255',
                                    'disabled',
                                    'class'=>'block w-10/12 mt-1 text-sm  
                                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                                    :shadow-outline-gray form-input', 'placeholder'=>'']) !!}
                                @if ($errors->has('ddlMunInci'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('ddlMunInci') }}</strong>
                                </span>
                                @endif
                            </label>

                            <label class="block text-sm w-10/12" id="uf_incidencia_id">
                                <span>&nbsp;<span>
                                {!! Form::select('ddlUFInci', $estados, $empresa->cidade->estado->id, [
                                    'id' => 'ddlUFInci', 
                                    'maxlength' => '255',
                                    'disabled',
                                    'class'=>'block w-full mt-1 text-sm  
                                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                                    :shadow-outline-gray form-input', 'placeholder'=>'']) !!}
                                @if ($errors->has('ddlUFInci'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('ddlUFInci') }}</strong>
                                </span>
                                @endif
                            </label>
                        </div>

                        <div class="grid md:grid-cols-4 gap-1 mt-4 mb-4">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Regimes Especiais de Tributação*</span>
                                {!! Form::select('ddlRegimeEspecial', $tipos_regime_esp_trib_mun
                                ,null, ['id'=> 'ddlRegimeEspecial','disabled','class'=>'block w-full mt-1 text-sm  
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                @if ($errors->has('ddlRegimeEspecial'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{ { $errors->first('ddlRegimeEspecial') }}</strong>
                                </span>
                                @endif
                            </label>

                            <label class="block text-sm">
                                <span class="text-gray-700 ">Tipo de Retenção do ISSQN</span>
                                {!! Form::select('ddlTipoRetencao', $tipos_retencoes
                                ,null, ['id'=> 'ddlTipoRetencao','disabled','class'=>'block w-full mt-1 text-sm  
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                @if ($errors->has('ddlTipoRetencao'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{ { $errors->first('ddlTipoRetencao') }}</strong>
                                </span>
                                @endif
                            </label>

                            <label class="block text-sm">
                                <span class="text-gray-700 ">Código Obra</span>
                                {!! Form::text('txtCodigoObra', old('txtCodigoObra'), ['disabled','class'=>'block w-full mt-1 text-sm  
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                                :shadow-outline-gray form-input', 'placeholder'=>'', 'id'=>'txtCodigoObra']) !!}
                                @if ($errors->has('txtCodigoObra'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('txtCodigoObra') }}</strong>
                                </span>
                                @endif
                            </label>

                             <label class="block text-sm">
                                <span class="text-gray-700 ">Deduções Base Cálc.*:</span>
                                <input disabled name="txtDeducaoBaseCalculo" type="text" maxlength="20" id="txtDeducaoBaseCalculo" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;" onblur="">
                            </label>
                        </div>

                        <div class="grid md:grid-cols-4 gap-1 mt-4 mb-4">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">ART</span>
                                {!! Form::text('txtArt', old('txtArt'), ['disabled','class'=>'block w-full mt-1 text-sm  
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                                :shadow-outline-gray form-input', 'placeholder'=>'', 'id'=>'txtArt']) !!}
                                @if ($errors->has('txtArt'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('txtArt') }}</strong>
                                </span>
                                @endif
                            </label>

                             <label class="block text-sm">
                                <span class="text-gray-700 ">Base de Cálculo do ISSQN</span>
                                <input disabled name="txtBaseCalculoISS" type="text" maxlength="20" id="txtBaseCalculoISS" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;" onblur="">
                            </label>

                             <label class="block text-sm">
                                <span class="text-gray-700 ">Aliq. ISSQN</span>
                                <input value="{{ number_format($atividade->aliquota,2, ',', '') }}" disabled name="txtAliquota" type="text" maxlength="5" id="txtAliquota" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;" onblur="">
                            </label>

                             <label class="block text-sm">
                                <span class="text-gray-700 ">Valor ISSQN</span>
                                <input disabled name="txtValorISSQN" type="text" maxlength="5" id="txtValorISSQN" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;" onblur="">
                            </label>

                             <label class="block text-sm">
                                <span class="text-gray-700 ">Valor ISSQN Retido</span>
                                <input disabled name="txtValorRetido" type="text" maxlength="20" id="txtValorRetido" placeholder="0,00" class="block w-full mt-1 text-sm  
              focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
              :shadow-outline-gray form-input" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;" onblur="">
                            </label>
                        </div>


                        <h4 class="mb-4 mt-4 text-base font-semibold text-white bg-gray-500 py-4 px-0 rounded-md">
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
                    :shadow-outline-gray form-input" oninput="FormataMoeda(this.name,event);" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;">
                                </label>
                            </div>
                            <div id="divValorCOFINS">
                                <label class="block text-sm">
                                    <span class="text-gray-700">Valor COFINS</span>
                                    <input placeholder="0,00" name="txtValorCOFINS" type="text" maxlength="22" id="txtValorCOFINS" disabled="disabled" class="block w-full mt-1 text-sm  
                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                    :shadow-outline-gray form-input" oninput="FormataMoeda(this.name,event);" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;">
                                </label>
                            </div>
                            <div id="divValorCSLL">
                                <label class="block text-sm">
                                    <span class="text-gray-700">Valor CSLL</span>
                                    <input placeholder="0,00" name="txtValorCSLL" type="text" maxlength="22" onchange="" onkeypress="" language="javascript" id="txtValorCSLL" disabled="disabled" class="block w-full mt-1 text-sm  
                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                    :shadow-outline-gray form-input" oninput="FormataMoeda(this.name,event);" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;">
                                </label>
                            </div>
                            
                            <div id="divValorIRRF">
                                <label class="block text-sm">
                                    <span class="text-gray-700">Valor IRRF</span>
                                    <input placeholder="0,00" name="txtValorIRRF" type="text" maxlength="22" onchange="" onkeypress="" language="javascript" id="txtValorIRRF" class="block w-full mt-1 text-sm  
                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                    :shadow-outline-gray form-input" oninput="FormataMoeda(this.name,event);" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;">
                                </label>
                            </div>
                            <div id="divValorCP">
                                 <label class="block text-sm">
                                    <span class="text-gray-700">Valor CP</span>
                                    <input placeholder="0,00" name="txtValorCP" type="text" maxlength="22" onchange="" language="javascript" id="txtValorCP" class="block w-full mt-1 text-sm  
                    focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                    :shadow-outline-gray form-input" oninput="FormataMoeda(this.name,event);" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;">
                                 </label>
                            </div>
                        </div>

                        <h4 class="mb-4 mt-4 text-base font-semibold text-white bg-gray-500 py-4 px-0 rounded-md">
                            Valores Aproximados dos Tributos
                        </h4>

                        <div class="grid md:grid-cols-2 gap-1 mt-4 mb-4">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Escolha o Tipo de Informação:</span>
                                    <select name="ddlTipoInfo" onchange="" language="javascript" id="ddlTipoInfo" 
                                    class="block w-full mt-1 text-sm px-3 py-1.5 form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray">
                                        <option selected="selected" value="1">Valores Percentuais</option>
                                        <option value="2">Valores Monetários</option>
                                    </select>
                                    @if ($errors->has('ddlTipoInfo'))
                                        <span class="text-xs text-red-600 ">
                                        <strong>{{ $errors->first('ddlTipoInfo') }}</strong>
                                    </span>
                                    @endif
                            </label>

                            <div id="divPercentualTribSN">
                                <label class="block text-sm">
                                        <span class="text-gray-700">Percentual Tot. Tributos - SN*</span>
                                        <input required placeholder="0,00" name="txtPercentualTribSN" type="text" maxlength="22" onchange="" language="javascript" id="txtPercentualTribSN" class="block w-full mt-1 text-sm  
                        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                        :shadow-outline-gray form-input" oninput="FormataMoeda(this.name,event);" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;">
                                </label>
                            </div>
                        </div>


                        <h4 class="mb-4 mt-4 text-base font-semibold text-white bg-gray-500 py-4 px-0 rounded-md">
                            Imposto e Contribuição Sobre Bens e Servicos - IBS/CBS
                        </h4>

                        <div class="grid md:grid-cols-4 gap-1 mt-4 mb-4">
                            <label class="block text-sm">
                                <span class="text-gray-700 ">Indicação da Operação</span>
                                {!! Form::select('ddlIndicadorOperacao', $indOpIbsCbs
                                ,null, ['id'=> 'ddlIndicadorOperacao','class'=>'block w-full mt-1 text-sm  
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                @if ($errors->has('ddlIndicadorOperacao'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('ddlIndicadorOperacao') }}</strong>
                                </span>
                                @endif
                            </label>                            

                            <label class="block text-sm">
                                <span class="text-gray-700 ">Situação Tributária</span>
                                {!! Form::select('ddlSituacaoTributaria', $cstIbsCsb
                                ,null, ['required','id'=> 'ddlSituacaoTributaria','class'=>'block w-full mt-1 text-sm  
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                @if ($errors->has('ddlSituacaoTributaria'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('ddlSituacaoTributaria') }}</strong>
                                </span>
                                @endif
                            </label>

                            <label class="block text-sm">
                                <span class="text-gray-700 ">Classificação Tributária</span>
                                {!! Form::select('ddlClassificacaoTributaria', ['' => 'Selecione']
                                ,null, ['required','id'=> 'ddlClassificacaoTributaria','class'=>'block w-full mt-1 text-sm  
                                form-select
                                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                                @if ($errors->has('ddlClassificacaoTributaria'))
                                    <span class="text-xs text-red-600 ">
                                    <strong>{{ $errors->first('ddlClassificacaoTributaria') }}</strong>
                                </span>
                                @endif
                            </label>

                            <label class="block text-sm">
                                        <span class="text-gray-700">Base de Cálculo</span>
                                        <input readonly placeholder="0,00" name="txtBaseCalc" type="text" maxlength="22" onchange="" language="javascript" id="txtBaseCalc" class="block w-full mt-1 text-sm  
                        focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                        :shadow-outline-gray form-input" oninput="FormataMoeda(this.name,event);" onkeypress="SoNumeros(event); FormataMoeda(this.name,event);" onpaste="return false;">
                            </label>
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



