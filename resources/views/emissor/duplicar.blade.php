<x-area-empresa-layout title="Emissor NFSe">
    <!-- FULL WIDTH -->
    <div class="w-full px-4 py-0 sm:px-0 lg:px-0">
        <!-- FORM FULL -->
        <div class="w-full py-2">
            {!! Form::open(['route'=>'notas.store', 'files'=> true, 'name'=> 'Form1']) !!}
                @include('emissor._form')
            {!! Form::close() !!}
        </div>
    </div>


    <!-- Overlay de processamento -->
    <div id="overlay-calculo"
        class="fixed inset-0 z-[9999] hidden items-center justify-center">

        <div class="flex flex-col items-center gap-3 rounded-xl bg-white px-8 py-6 shadow-xl">

            <!-- Spinner -->
            <div class="h-12 w-12 rounded-full border-4 border-blue-200 border-t-blue-600">
                <svg fill="hsl(228, 97%, 42%)" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g><circle cx="12" cy="3" r="1"><animate id="spinner_7Z73" begin="0;spinner_tKsu.end-0.5s" attributeName="r" calcMode="spline" dur="0.6s" values="1;2;1" keySplines=".27,.42,.37,.99;.53,0,.61,.73"/></circle><circle cx="16.50" cy="4.21" r="1"><animate id="spinner_Wd87" begin="spinner_7Z73.begin+0.1s" attributeName="r" calcMode="spline" dur="0.6s" values="1;2;1" keySplines=".27,.42,.37,.99;.53,0,.61,.73"/></circle><circle cx="7.50" cy="4.21" r="1"><animate id="spinner_tKsu" begin="spinner_9Qlc.begin+0.1s" attributeName="r" calcMode="spline" dur="0.6s" values="1;2;1" keySplines=".27,.42,.37,.99;.53,0,.61,.73"/></circle><circle cx="19.79" cy="7.50" r="1"><animate id="spinner_lMMO" begin="spinner_Wd87.begin+0.1s" attributeName="r" calcMode="spline" dur="0.6s" values="1;2;1" keySplines=".27,.42,.37,.99;.53,0,.61,.73"/></circle><circle cx="4.21" cy="7.50" r="1"><animate id="spinner_9Qlc" begin="spinner_Khxv.begin+0.1s" attributeName="r" calcMode="spline" dur="0.6s" values="1;2;1" keySplines=".27,.42,.37,.99;.53,0,.61,.73"/></circle><circle cx="21.00" cy="12.00" r="1"><animate id="spinner_5L9t" begin="spinner_lMMO.begin+0.1s" attributeName="r" calcMode="spline" dur="0.6s" values="1;2;1" keySplines=".27,.42,.37,.99;.53,0,.61,.73"/></circle><circle cx="3.00" cy="12.00" r="1"><animate id="spinner_Khxv" begin="spinner_ld6P.begin+0.1s" attributeName="r" calcMode="spline" dur="0.6s" values="1;2;1" keySplines=".27,.42,.37,.99;.53,0,.61,.73"/></circle><circle cx="19.79" cy="16.50" r="1"><animate id="spinner_BfTD" begin="spinner_5L9t.begin+0.1s" attributeName="r" calcMode="spline" dur="0.6s" values="1;2;1" keySplines=".27,.42,.37,.99;.53,0,.61,.73"/></circle><circle cx="4.21" cy="16.50" r="1"><animate id="spinner_ld6P" begin="spinner_XyBs.begin+0.1s" attributeName="r" calcMode="spline" dur="0.6s" values="1;2;1" keySplines=".27,.42,.37,.99;.53,0,.61,.73"/></circle><circle cx="16.50" cy="19.79" r="1"><animate id="spinner_7gAK" begin="spinner_BfTD.begin+0.1s" attributeName="r" calcMode="spline" dur="0.6s" values="1;2;1" keySplines=".27,.42,.37,.99;.53,0,.61,.73"/></circle><circle cx="7.50" cy="19.79" r="1"><animate id="spinner_XyBs" begin="spinner_HiSl.begin+0.1s" attributeName="r" calcMode="spline" dur="0.6s" values="1;2;1" keySplines=".27,.42,.37,.99;.53,0,.61,.73"/></circle><circle cx="12" cy="21" r="1"><animate id="spinner_HiSl" begin="spinner_7gAK.begin+0.1s" attributeName="r" calcMode="spline" dur="0.6s" values="1;2;1" keySplines=".27,.42,.37,.99;.53,0,.61,.73"/></circle><animateTransform attributeName="transform" type="rotate" dur="6s" values="360 12 12;0 12 12" repeatCount="indefinite"/></g></svg>
            </div>

            <p class="text-lg font-semibold text-gray-700">
                Ajustando valores...
            </p>

            <p class="text-sm text-gray-500">
                Aguarde enquanto os cálculos são atualizados.
            </p>

        </div>
    </div>

 @section('jquery')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"
                integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="https://cdn.es.gov.br/scripts/jquery/jquery-maskedinput/1.4.1/jquery.maskedinput-1.4.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-masker/1.2.0/vanilla-masker.min.js"
            integrity="sha512-RbMQw6xKGymv6bRMO4z5OxHBzzem7BPEQX7nTJC9G08A70gXdUka76Rvgey83MsSXrIEJddog0vxUKN6iTce2Q=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                var somenteNumeros = function(valor){
                    return valor.replace(/\D/g, '');
                };
                var trim = function(valor){
                    return valor.replace(" ", "");
                };
            });
        </script>
        <script>
            const tomadorExterior = @js($tomador->cidade()->first()->codigo == 99999);
        </script>
        <script src="{{ asset('js/fn_geral.js') }}"></script>
        <script src="{{ asset('js/calculos_geral.js?'.time()) }}"></script>
        <script src="{{ asset('js/tela_nfse.js?'.time()) }}"></script>

        <script type="text/javascript">
            function ativarOverlay() {
                $('#overlay-calculo')
                    .removeClass('hidden')
                    .addClass('flex');
            }

            function desativarOverlay() {
                $('#overlay-calculo')
                    .removeClass('flex')
                    .addClass('hidden');
            }

            $(function(){
                const $campoSelectNbs  = $('#nbs');
                const $campoSelectDdlSitTribFederal = $('#ddlSitTribFederal');
                
                const oldData = {
                    empresa_id: "{{ old('empresa_id') }}",
                    cTribMun: "{{ old('empresa_atividade_id') }}",
                    cTribNac: "{{ old('cTribNac') }}",
                    nbs: "{{ old('nbs') }}"
                };

                $campoSelectNbs.prop("disabled", false);
                $campoSelectNbs.css('background-color', '#D3D3D3');

                $campoSelectDdlSitTribFederal.prop("disabled", true);
                $campoSelectDdlSitTribFederal.css('background-color', '#D3D3D3');
                
                $('#ddlPaisPrestacao').change(function(e){
                    e.preventDefault();
                    if($(this).val() != 26){
                        $('#ddlEstadoPrestacao').prop("disabled", true).css('background-color', '#D3D3D3').empty();
                        $('#ddlCidadePrestacao').prop("disabled", true).css('background-color', '#D3D3D3').empty();
                    }else{
                        $('#ddlEstadoPrestacao').prop("disabled", false).css('background-color', '');
                        $('#ddlCidadePrestacao').prop("disabled", false).css('background-color', '');

                        $.ajax({
                            type: 'GET',
                            url: base_url + '/consultar/estados/json',
                            dataType: 'json',
                            cache: false,

                            success: function(data) {
                                const $estados = $('#ddlEstadoPrestacao');
                                $estados.empty();
                                $estados.append(
                                    $('<option>', {
                                        value: '',
                                        text: 'Selecione o Estado'
                                    })
                                );

                                $.each(data, function(index, uf) {
                                    $estados.append(
                                        $('<option>', {
                                            value: uf.id,
                                            text: uf.nome
                                        })
                                    );
                                });
                            },

                            error: function(xhr) {
                                console.log('ERRO:', xhr);
                                console.log(xhr.responseText);

                                alert('Verifique os dados Informados e tente novamente.');
                            }
                        });
                    }
                });

                $('#txtUFObras').change(function(e){
                    if( $(this).val() != '' ) {
                        e.preventDefault();
          
                        $.ajax({
                            type: 'GET',
                            url: base_url + '/consultar/cidades/' + $(this).val() + '/json',
                            dataType: 'json',
                            cache: false,

                            success: function(data) {
                                const $cidade = $('#txtCidadeObras');
                                $cidade.empty();
                                $cidade.append(
                                    $('<option>', {
                                        value: '',
                                        text: 'Selecione a Cidade'
                                    })
                                );

                                $.each(data, function(index, cidade) {
                                    $cidade.append(
                                        $('<option>', {
                                            value: cidade.codigo,
                                            text: cidade.municipio
                                        })
                                    );
                                });
                            },

                            error: function(xhr) {
                                console.log('ERRO:', xhr);
                                console.log(xhr.responseText);

                                alert('Verifique os dados Informados e tente novamente.');
                            }
                        });
                    } 
                });                

                $('#ddlEstadoPrestacao').change(function(e){
                    if( $(this).val() != '' ) {
                        e.preventDefault();
          
                        $.ajax({
                            type: 'GET',
                            url: base_url + '/consultar/cidades/' + $(this).val() + '/json',
                            dataType: 'json',
                            cache: false,

                            success: function(data) {
                                const $cidade = $('#ddlCidadePrestacao');
                                $cidade.empty();
                                $cidade.append(
                                    $('<option>', {
                                        value: '',
                                        text: 'Selecione a Cidade'
                                    })
                                );

                                $.each(data, function(index, cidade) {
                                    $cidade.append(
                                        $('<option>', {
                                            value: cidade.codigo,
                                            text: cidade.municipio
                                        })
                                    );
                                });
                            },

                            error: function(xhr) {
                                console.log('ERRO:', xhr);
                                console.log(xhr.responseText);

                                alert('Verifique os dados Informados e tente novamente.');
                            }
                        });
                    } 
                });

                $('#empresa_atividade_id').on('change', function () {
                    carregarCodTributacaoNac(
                        oldData.cTribMun,
                        $(this).val()
                    );
                });

                $('#cTribNac').on('change', function () {
                    carregarNbs(
                        oldData.cTribNac,
                        $(this).val()
                    );

                    $('#nbs').prop("disabled", false);
                    $campoSelectNbs.css('background-color', '');
                });

                if (oldData.cTribNac) {
                    carregarNbs(
                        oldData.nbs,    
                        oldData.cTribNac                   
                    );
                }

                $campoSelectNbs.on('change', function () {
                    $campoSelectDdlSitTribFederal.prop("disabled", false);
                    $campoSelectDdlSitTribFederal.css('background-color', '');
                });
            });

            function carregarCodTributacaoNac(tribNacSelecionada = null, cTribMun) {
                let cTribNacSelect = $('#cTribNac');

                cTribNacSelect.empty();
                cTribNacSelect.append('<option value="">Código Tributação Nacional</option>');

                $.getJSON('/c/emissor/obter/tributacao-nacional/por-tributacao-mun?cTribMun=' + cTribMun, function (data) {
                    $.each(data, function (_, tribNac) {
                        cTribNacSelect.append(
                            $('<option>', {
                                value: tribNac.cTribNac,
                                text: tribNac.descricao,
                                selected: tribNac.id == tribNacSelecionada
                            })
                        );

                    });
                });
            }

            function carregarNbs(nbsSelecionado= null, cTribNac) {
                let nbsSelect = $('#nbs');

                nbsSelect.empty();
                nbsSelect.append('<option value="">NBS</option>');

                $.getJSON('/c/emissor/obter/nbs/por-empresa?cTribNac=' + cTribNac, function (data) {
                    $.each(data, function (_, nbs) {
                        nbsSelect.append(
                            $('<option>', {
                                value: nbs.codigo,
                                text: nbs.descricao,
                                selected: nbs.codigo == nbsSelecionado
                            })
                        );

                    });
                });
            }
        </script>

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
            var cep = document.querySelector('#txtCepObras');
            VMasker(cep).maskPattern(cepMask[0]);
            cep.addEventListener('input', inputHandler.bind(undefined, cepMask, 14), false);
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
                                    console.log(result);
                                    $("#txtCidadeObras").val(result.city).trigger('change');
                                    $("#txtBairroObras").val(result.district);//bairro
                                    $("#txtEnderecoObras").val(result.address);
                                    //$('#txtUFObras').val(result.state);
                                    console.log('Achou o CEP na API CEP.');
                                } 
                            }
                        ).fail(function() {
                            console.log('Erro API CEP.');
                            //buscarViaCep(cep);
                        });
                    }
                };

                $('#txtCepObras').on('keyup', function(e){
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

                $('#txtCepObras').on('blur', function(e){
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
            /*
            Manter campo preenchidos
            */
            
            $(document).ready(function () {
                let nbs = @json(old('nbs', $nota['nbs']));
                let atividade_municipal = @json(old('empresa_atividade_id'));
                let ddlTribISSQN        = @json(old('ddlTribISSQN'));
                let ddlRegimeEspecial   = @json(old('ddlRegimeEspecial'));
                let ddlSitTribFederal   = @json(old('ddlSitTribFederal'));
                let ddlTipoRetFederal   = @json(old('ddlTipoRetFederal'));
                let ddlIndicadorOperacao = @json(old('ddlIndicadorOperacao'));
                let ddlSituacaoTributaria = @json(old('ddlSituacaoTributaria'));
                let ddlClassificacaoTributaria = @json(old('ddlClassificacaoTributaria'));
                let cTribNac = @json(old('cTribNac'));

                if (cTribNac) {
                    $('#cTribNac').prop('disabled', false);
                    $('#cTribNac').css('background-color', '');
                    $('#cTribNac').val(cTribNac)//.trigger('change');
                }

                if (nbs) {
                    $('#nbs').prop('disabled', false);
                    $('#nbs').css('background-color', '');
                    $('#nbs').val(nbs).trigger('change');
                }

                if (atividade_municipal) {
                    $('#empresa_atividade_id').prop('disabled', false);
                    $('#empresa_atividade_id').css('background-color', '');
                    $('#empresa_atividade_id').val(atividade_municipal).trigger('change');
                }

                //
                if (ddlTribISSQN) {
                    $('#ddlTribISSQN').prop('disabled', false);
                    $('#ddlTribISSQN').css('background-color', '');
                    $('#ddlTribISSQN').val(ddlTribISSQN).trigger('change');
                }

                if (ddlRegimeEspecial) {
                    $('#ddlRegimeEspecial').prop('disabled', false);
                    $('#ddlRegimeEspecial').css('background-color', '');
                    $('#ddlRegimeEspecial').val(ddlRegimeEspecial).trigger('change');
                }

                //ddlSitTribFederal
                if (ddlSitTribFederal) {
                    $('#ddlSitTribFederal').prop('disabled', false);
                    $('#ddlSitTribFederal').css('background-color', '');
                    $('#ddlSitTribFederal').val(ddlSitTribFederal).trigger('change');
                }

                //ddlTipoRetFederal
                if (ddlTipoRetFederal) {
                    $('#ddlTipoRetFederal').prop('disabled', false);
                    $('#ddlTipoRetFederal').css('background-color', '');
                    $('#ddlTipoRetFederal').val(ddlTipoRetFederal).trigger('change');
                }

                //ddlIndicadorOperacao
                if (ddlIndicadorOperacao) {
                    $('#ddlIndicadorOperacao').prop('disabled', false);
                    $('#ddlIndicadorOperacao').css('background-color', '');
                    $('#ddlIndicadorOperacao').val(ddlIndicadorOperacao).trigger('change');
                }

                //ddlSituacaoTributaria
                if (ddlSituacaoTributaria) {
                    $('#ddlSituacaoTributaria').prop('disabled', false);
                    $('#ddlSituacaoTributaria').css('background-color', '');
                    $('#ddlSituacaoTributaria').val(ddlSituacaoTributaria).trigger('change');
                }

                //ddlClassificacaoTributaria
                if (ddlClassificacaoTributaria) {
                    $('#ddlClassificacaoTributaria').prop('disabled', false);
                    $('#ddlClassificacaoTributaria').css('background-color', '');
                    $('#ddlClassificacaoTributaria').val(ddlClassificacaoTributaria).trigger('change');
                }
            });

            ativarOverlay();
            const esperar = (ms) => new Promise(resolve => setTimeout(resolve, ms));

            $(document).ready(async function () {
                let nota = @json($nota);

                $('#empresa_atividade_id').trigger('change');
                // Aguarda 2 segundos de forma limpa
                await esperar(2000); 
                             
                setTimeout(async function () {
                    $('#cTribNac').val(String(nota.cTribNac))
                    $('#cTribNac').trigger('change');
                    
                    await esperar(2000); 

                    $('#nbs')
                        .prop('disabled', false)
                        .css('background-color', '')
                        .val(String(nota.nbs))
                        .trigger('change');

                    $('#ddlTribISSQN').trigger('change');
                    $('#ddlRegimeEspecial').trigger('change');
                    $('#ddlTipoRetencao').trigger('change');
                    $('#ddlSitTribFederal').trigger('change');
                    $('#ddlTipoRetFederal').val(String(nota.ddlTipoRetFederal));

                    $('#ddlIndicadorOperacao').trigger('change');
                    $('#ddlSituacaoTributaria').val(String(nota.ddlSituacaoTributaria));
                    $('#ddlSituacaoTributaria').trigger('change');  
                    
                     // Aguarda 2 segundos de forma limpa
                    await esperar(2000); 

                    $('#ddlClassificacaoTributaria').val(String(nota.ddlClassificacaoTributaria));
                    desativarOverlay();
                }, 8000); // 2000 ms = 2 segundos
            }); 
        </script>
    @endsection
</x-area-empresa-layout>