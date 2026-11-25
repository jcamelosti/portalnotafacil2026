<x-area-cliente-layout title="Método de Pagamento">

<div class="py-6 w-full px-4 space-y-4">

    {{-- HEADER --}}
    <div class="bg-white shadow rounded-lg p-4">
        <h1 class="text-lg font-semibold">
            Renovação de Licença de Uso
        </h1>

        <p class="text-sm text-gray-500">
            Escolha um método de pagamento para continuar
        </p>
    </div>

    @include('components.mensagens')

    {{-- RESUMO: EMPRESA + VALOR --}}
    <div class="bg-white shadow rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>
            <div class="text-xs text-gray-500">Empresa</div>
            <div class="font-semibold text-lg">
                {{ $fatura->empresa()->first()->razao_social }}
            </div>
        </div>

        <div class="sm:text-right">
            <div class="text-xs text-gray-500">Valor a pagar</div>
            <div class="text-green-600 font-bold text-2xl">
                R$ {{ number_format($fatura->total, 2, ',', '.') }}
            </div>
        </div>

    </div>

    {{-- MÉTODOS DE PAGAMENTO --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- MERCADO LIVRE --}}
        <div class="bg-white shadow rounded-lg p-5 hover:shadow-md transition flex flex-col">

            <div class="text-xs text-gray-500">Opção 1</div>
            <div class="font-semibold text-lg">Mercado Livre</div>

            <div class="mt-2 mb-4">
                <span class="inline-block bg-gray-100 text-gray-700 text-xs font-medium px-2 py-1 rounded">Pix</span>
            </div>

            <ul class="text-sm text-gray-600 space-y-1 mb-4">
                <li>• Liberação automática</li>
                <li>• Confirmação em até 10 minutos</li>
                <li>• Não exige código de confirmação no WhatsApp</li>
            </ul>

            <p class="text-sm text-gray-500 mb-4">
                Recomendado se você <strong>não tem acesso ao WhatsApp</strong> do número cadastrado.
            </p>

            <a href="{{ route('licenca.gerar-pix', base64_encode($fatura->id)) }}"
               class="mt-auto block text-center bg-{{ $cores[0] }}-600 hover:bg-{{ $cores[0] }}-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Pagar com Mercado Livre
            </a>

        </div>

        {{-- INFINITEPAY --}}
        <div class="bg-white shadow rounded-lg p-5 hover:shadow-md transition flex flex-col">

            <div class="text-xs text-gray-500">Opção 2</div>
            <div class="font-semibold text-lg">InfinitePay</div>

            <div class="mt-2 mb-4 flex gap-2">
                <span class="inline-block bg-gray-100 text-gray-700 text-xs font-medium px-2 py-1 rounded">Pix</span>
                <span class="inline-block bg-gray-100 text-gray-700 text-xs font-medium px-2 py-1 rounded">Cartão de Crédito</span>
            </div>

            <ul class="text-sm text-gray-600 space-y-1 mb-4">
                <li>• Liberação automática</li>
                <li>• Confirmação em até 10 minutos</li>
            </ul>

            {{-- ALERTA WHATSAPP --}}
            <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-3 flex items-start gap-2 mb-4">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>

                <p class="text-sm text-gray-700">
                    É preciso ter acesso ao <strong>WhatsApp do número cadastrado</strong>:
                    a InfinitePay envia um <strong>código de confirmação</strong> para ele.
                    Sem acesso, use o <strong>Mercado Livre (Pix)</strong>.
                </p>

            </div>

            <a href="{{ route('licenca.inifite-pay-gerar-pix', base64_encode($fatura->id)) }}"
               class="mt-auto block text-center bg-{{ $cores[2] }}-600 hover:bg-{{ $cores[2] }}-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Pagar com InfinitePay
            </a>

        </div>

    </div>

    {{-- INFORMAÇÕES GERAIS --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start gap-3">

        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
        </svg>

        <p class="text-sm text-gray-700">
            Ao clicar no botão, você será levado à página de pagamento da plataforma escolhida.
            O Pix cai na hora, mas a confirmação pode levar alguns minutos para aparecer aqui no sistema.
        </p>

    </div>

</div>

@section('jquery')
@endsection

</x-area-cliente-layout>
