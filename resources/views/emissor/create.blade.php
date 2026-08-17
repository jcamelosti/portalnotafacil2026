<x-area-empresa-layout title="Nota" class="text-4xl">
{{--    <x-section-title title="Pedido de Cartão de Visitas" description="Cartão de visita digital personalizado grátis" />--}}
    <div class="md:py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        @include('components.mensagens')
        <div class="container grid mx-auto py-4 max-w-10xl">
            @if ( request()->routeIs('nota.emitir') || request()->routeIs('notas.duplicar') )
                {!! Form::open(['route'=>'notas.store', 'files'=> true, 'name'=> 'Form1']) !!}
                @include('emissor._form')
                {!! Form::close() !!}
            @else
                {!! Form::open(['route'=>'notas.store-substituicao', 'files'=> true, 'name'=> 'Form1']) !!}
                @include('emissor._form')
                {!! Form::close() !!}
            @endif
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
                              $('.localPrestacaoServico').append(result);
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
            
            $('#empresa_cnae_id').change(function(e){
                $('#item_lc_container label').remove();
                if( $(this).val() != '' ) {
                    e.preventDefault();

                    $.ajax({
                    type      : 'GET',
                    url: base_url + '/consultar/item-lc/' + $(this).val(),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success : function(result){
                        //$('#cidade_id').remove();
                        $('#item_lc_container').append(result);
                    },
                    error : function(){
                        alert('Verifique os dados Informados e tente novamente.');
                    }
                    });
                } else {
                    alert('Erro ao selecionar item lc.');
                }
            });


            //$('#item_lc_id').change(function(e){
            $(document).on('change', '#item_lc_id', function (e) {
                
                $('#nbs_container label').remove();
                
                if( $(this).val() != '' ) {
                    e.preventDefault();

                    $.ajax({
                    type      : 'GET',
                    url: base_url + '/consultar/nbs/' + $(this).val(),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success : function(result){
                        //$('#cidade_id').remove();
                        $('#nbs_container').append(result);
                    },
                    error : function(){
                        alert('Verifique os dados Informados e tente novamente.');
                    }
                    });
                } else {
                    alert('Erro ao selecionar item lc.');
                }
            });
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                @if(request()->routeIs('notas.substitucao') || request()->routeIs('notas.duplicar'))
                    const campo = document.getElementById('txtAliq');
                    if (campo) {
                        campo.focus();

                        // opcional: selecionar o conteúdo
                        campo.select();
                    }
                @endif
            });
        </script>
    @endsection
</x-area-empresa-layout>