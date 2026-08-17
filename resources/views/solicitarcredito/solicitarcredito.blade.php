<x-area-cliente-layout title="Solicitar Crédito">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Solicitar Crédito') }}
        </h2>
    </x-slot>

    @include('components.mensagens')

    <div class="py-1 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="text-center my-10">
            <h1 class="font-bold text-xl mb-2">Solicitação de Crédito para Uso do Sistema</h1>
        </div>

         {{-- AVISO PAGAMENTO --}}
        <div class="bg-blue-50 border border-blue-200 shadow rounded-lg p-4 flex items-start gap-3">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-6 h-6 text-blue-600 flex-shrink-0 mt-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">

                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>

            </svg>

            <!--p class="text-sm text-gray-700">
                Os pagamentos são processados pela <strong>InfinitePay</strong>.
                Após a realização do pagamento pode levar alguns minutos para que
                a confirmação seja recebida em nosso sistema.

                <br><br>

                <strong>Pagamentos via Pix são creditados imediatamente</strong>,
                porém a notificação de confirmação pode levar alguns minutos para
                aparecer na plataforma.

                <br><br>
                Ao clicar em "Inserir Crédito Agora", você será redirecionado para a plataforma de pagamento da InfinitePay.
            </p-->
            
            <p class="text-sm text-gray-700">
                Os pagamentos são processados pela <strong>Mercado Pago</strong>.
                Após a realização do pagamento pode levar alguns minutos para que
                a confirmação seja recebida em nosso sistema.

                <br><br>

                <strong>Pagamentos via Pix são creditados imediatamente</strong>,
                porém a notificação de confirmação pode levar alguns minutos para
                aparecer na plataforma.
            </p>
        </div>

        <div class="mx-12 p-4 space-y-12 lg:space-y-0 lg:flex lg:gap-4 lg:items-center lg:justify-center">
            <div class="max-w-sm p-8 border-t-4 border-green-600 rounded shadow-lg">
                <p class="text-center font-bold text-2">Informe o Valor que Deseja Inserir</p>
                <div class="w-full md:w-3/5 py-6 text-center">
                    <div class="px-4 py-3 mb-8 bg-white ">
                        {!! Form::open(['route'=>'creditos.confirmar', 'method'=>'POST']) !!}
                        <label class="block text-sm">
                            {!! Form::select('valor_credito', $valores, 5, ['class'=>'block w-full mt-1 text-sm    form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                        </label>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                                <button type="submit" type="button"
                                        class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                                Inserir Crédito Agora
                              </button>
                            </span>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('jquery')
    @endsection
</x-area-cliente-layout>