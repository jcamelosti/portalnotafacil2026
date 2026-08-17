<x-area-cliente-layout title="Método de Pagamento">

<div class="py-6 w-full px-4 space-y-4">

    {{-- HEADER --}}
    <div class="bg-white shadow rounded-lg p-4">
        <div>
            <h1 class="text-lg font-semibold">
                Renovação de Licença de Uso
            </h1>

            <p class="text-sm text-gray-500">
                Escolha um método de pagamento para continuar
            </p>
        </div>
    </div>

    @include('components.mensagens')

    {{-- EMPRESA --}}
    <div class="bg-white shadow rounded-lg p-4">
        <h3 class="text-sm text-gray-500">Empresa</h3>

        <div class="font-semibold text-lg">
            {{ $fatura->empresa()->first()->razao_social }}
        </div>
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

        <p class="text-sm text-gray-700">
            Os pagamentos são processados pela <strong>InfinitePay</strong>.
            Após a realização do pagamento pode levar alguns minutos para que
            a confirmação seja recebida em nosso sistema.

            <br><br>

            <strong>Pagamentos via Pix são creditados imediatamente</strong>,
            porém a notificação de confirmação pode levar alguns minutos para
            aparecer na plataforma.

            <br><br>
            Ao clicar em "Pagar Agora", você será redirecionado para a plataforma de pagamento da InfinitePay.
        </p>

    </div>


    {{-- MÉTODOS DE PAGAMENTO --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

        {{-- PIX --}}
        <div class="bg-white shadow rounded-lg p-4 hover:shadow-md transition">

            <div class="text-xs text-gray-500">
                Método
            </div>

            <div class="font-semibold text-lg mb-2">
                Pagamento via PIX/Cartão de Crédito
            </div>

            <div class="text-xs text-gray-500">
                Valor
            </div>

            <div class="text-green-600 font-bold text-xl mb-3">
                R$ {{ number_format($fatura->total,2, ',', '.') }}
            </div>

            <ul class="text-sm text-gray-600 space-y-1 mb-4">
                <li>• Liberação automática</li>
                <li>• Compensação em até 10 minutos</li>
            </ul>

            <a href="{{ route('licenca.gerar-pix', base64_encode($fatura->id)) }}"
               class="block text-center bg-{{ $cores[0] }}-600 hover:bg-{{ $cores[0] }}-700 text-white px-4 py-2 rounded-lg text-sm font-medium">

                Pagar Agora

            </a>
        </div>
    </div>
</div>

@section('jquery')
@endsection

</x-area-cliente-layout>