<x-area-cliente-layout title="Leitura QRCode - PIX">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Leitura QRCode - PIX') }}
        </h2>
    </x-slot>

    @if(!isset($naoMostra))
        <div class="py-10 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
                <h1 class="px-4 py-3">
                    Chave PIX
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Empresa: {{ $fatura->empresa()->first()->razao_social }}
                    </p>
                </h1>
            </div>
        </div>
    @endif

    @include('components.mensagens')

    <div class="py-1 mx-auto sm:px-6 lg:px-8">
        <div class="text-center my-10">
            <h1 class="font-bold text-xl">Pagamento Via PIX</h1>
            @if(isset($naoMostra))
                <h2 class="font-bold text-xl mb-2">Assim que o pagamento for efetuado o crédito será inserido em sua conta.<h2>
            @endif
            
            <div id="mensagemAviso" class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg  " role="alert">
                <span class="font-medium">Aviso Importante!</span> Ao efetuar o Pagamento aguarde a confirmação.
            </div>
        </div>

        <div id="area-qrcode" class="mx-12 space-y-12 lg:space-y-0 lg:flex lg:gap-4 lg:items-center lg:justify-center">
            <div class="p-8 border-t-4 border-green-600 rounded shadow-lg">
                <p class="text-center font-bold text-4xl">R$ {{ number_format($fatura->total,2, ',', '.') }}</p>
                <div class="w-full md:w-3/5 py-6 text-center content-center">
                    <img class="container max-w-screen-lg mx-auto text-center p-0 max-w-full h-auto" style="width: 220px" src="data:image/png;base64,{{ $fatura->qrcode_base64 }}">
                    <h5 class="font-bold">Copia e Cola</h5>
                    <p class="mt-2 break-words">{{ $fatura->qrcode_digitavel }}</p>
                </div>
            </div>
        </div>

        <div style="display: none;" id="alert-additional-content-3" class="p-4 mb-4 border border-green-300 rounded-lg bg-green-50 " role="alert">
            <div class="flex items-center">
              <svg aria-hidden="true" class="w-5 h-5 mr-2 text-green-700 " fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
              <span class="sr-only">Info</span>
              <h3 class="text-lg font-medium text-green-700 ">Pagamento Confirmado</h3>
            </div>
            <div class="mt-2 mb-4 text-sm text-green-700 ">
               Recebemos seu pagamento. Obrigado!
            </div>
            <!--div class="flex">
              <button type="button" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-xs px-3 py-1.5 mr-2 text-center inline-flex items-center  :bg-green-900">
                <svg aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path></svg>
                View more
              </button>
              <button type="button" class="text-green-700 bg-transparent border border-green-700 hover:bg-green-800 hover:text-white focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-xs px-3 py-1.5 text-center   :text-white" data-dismiss-target="#alert-additional-content-3" aria-label="Close">
                Dismiss
              </button>
            </div-->
        </div>
    </div>

    @section('jquery')
        <script>
            let transactionId = '{{ $fatura->transacao_id }}';
                       
            $(document).ready(function(){
               let urlConsulta = base_url + '/checar/retorno/mercadopago/' + transactionId;
               setInterval(function() {
                    if(!$('#alert-additional-content-3').is(":visible")){
                        $.get( urlConsulta, function(data) {
                            if(data.payment_status == true){
                                $('#area-qrcode').hide();
                                $('#mensagemAviso').hide();
                                $('#alert-additional-content-3').show();
                            }
                        })
                        .done(function(data) {
                            //
                        })
                        .fail(function() {
                            
                        })
                        .always(function() {
                            
                        });
                    }else{
                        
                    }
               }, 5000);
            });
        </script>
    @endsection
</x-area-cliente-layout>

