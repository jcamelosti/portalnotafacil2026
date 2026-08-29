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

 @section('jquery')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"
                integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="https://cdn.es.gov.br/scripts/jquery/jquery-maskedinput/1.4.1/jquery.maskedinput-1.4.1.min.js"></script>
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

        <script src="{{ asset('js/fn_geral.js') }}"></script>
        <script src="{{ asset('js/calculos_geral.js?'.time()) }}"></script>
        <script src="{{ asset('js/tela_nfse.js?'.time()) }}"></script>

        <script type="text/javascript">
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
    @endsection
</x-area-empresa-layout>