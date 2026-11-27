<x-area-cliente-layout title="Solicitar Crédito">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Solicitar Crédito') }}
        </h2>
    </x-slot>

    @php
        // $valores: ['64,50' => '5 licenças - R$ 64,50', ...] — a chave é o valor enviado no formulário
        $opcoes = collect($valores)->map(function ($rotulo, $valor) {
            [$qtd] = array_pad(explode(' - ', $rotulo, 2), 1, $rotulo);
            $numQtd = (int) $qtd;
            $total = (float) str_replace(',', '.', str_replace('.', '', $valor));
            return [
                'valor' => (string) $valor,
                'rotulo' => $rotulo,
                'qtd' => trim($qtd),
                'unitario' => $numQtd > 0 ? $total / $numQtd : null,
            ];
        })->values();
        $selecionado = old('valor_credito', optional($opcoes->first())['valor']);
        $maiorQtd = $opcoes->count() > 1 ? $opcoes->last()['valor'] : null;
    @endphp

    <div class="py-10 mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

        {{-- Cabeçalho --}}
        <div class="text-center mb-8">
            <span class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wide text-green-700 bg-green-100 rounded-full">
                Créditos
            </span>
            <h1 class="mt-3 font-bold text-3xl text-gray-800">Solicitação de Crédito para Uso do Sistema</h1>
            <p class="mt-2 text-sm text-gray-500">Escolha a quantidade de licenças que deseja adicionar à sua conta.</p>
        </div>

        @include('components.mensagens')

        {!! Form::open(['route' => 'creditos.confirmar', 'method' => 'POST']) !!}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden"
             x-data="{ sel: {{ json_encode((string) $selecionado) }}, rotulos: {{ json_encode($opcoes->pluck('rotulo', 'valor')) }} }">

            <div class="p-6 md:p-8">
                <h2 class="text-base font-semibold text-gray-800">Informe o valor que deseja inserir</h2>

                @if($opcoes->isEmpty())
                    <p class="mt-4 text-sm text-gray-500">Nenhuma opção de crédito disponível no momento.</p>
                @else
                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($opcoes as $op)
                            <label class="credito-opcao relative block cursor-pointer">
                                <input type="radio" name="valor_credito" value="{{ $op['valor'] }}" class="sr-only"
                                       x-model="sel" {{ (string) $selecionado === $op['valor'] ? 'checked' : '' }}>
                                <div class="credito-card h-full rounded-xl border-2 border-gray-200 bg-white p-5 text-center">
                                    @if($op['valor'] === $maiorQtd)
                                        <span class="credito-badge px-2 py-1 text-xs font-bold text-white rounded-full bg-green-600 shadow">
                                            Mais licenças
                                        </span>
                                    @endif
                                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ $op['qtd'] }}</div>
                                    <div class="mt-2 flex items-baseline justify-center">
                                        <span class="text-base font-semibold text-gray-700 mr-1">R$</span>
                                        <span class="text-3xl font-extrabold text-gray-900 leading-none">{{ $op['valor'] }}</span>
                                    </div>
                                    @if($op['unitario'])
                                        <div class="mt-2 text-xs text-gray-500">
                                            R$ {{ number_format($op['unitario'], 2, ',', '.') }} por licença
                                        </div>
                                    @endif
                                    <div class="credito-check mt-3 text-xs font-semibold text-green-700">✓ Selecionado</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Rodapé com resumo e ação --}}
            <div class="bg-gray-50 border-t border-gray-200 px-6 md:px-8 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Selecionado:
                    <strong class="text-gray-900" x-text="rotulos[sel] || '—'">
                        {{ $opcoes->firstWhere('valor', (string) $selecionado)['rotulo'] ?? '—' }}
                    </strong>
                </div>
                <button type="submit" {{ $opcoes->isEmpty() ? 'disabled' : '' }}
                        class="credito-btn inline-flex justify-center items-center px-8 py-3 rounded-full text-base font-semibold text-white bg-green-600 hover:bg-green-500 shadow-lg focus:outline-none focus:shadow-outline-green">
                    Inserir Crédito Agora
                </button>
            </div>
        </div>
        {!! Form::close() !!}

        {{-- AVISO PAGAMENTO --}}
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-5 flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
            </svg>

            {{--
            <p class="text-sm text-gray-700">
                Os pagamentos são processados pela <strong>InfinitePay</strong>.
                Após a realização do pagamento pode levar alguns minutos para que
                a confirmação seja recebida em nosso sistema.
                <br><br>
                <strong>Pagamentos via Pix são creditados imediatamente</strong>,
                porém a notificação de confirmação pode levar alguns minutos para
                aparecer na plataforma.
                <br><br>
                Ao clicar em "Inserir Crédito Agora", você será redirecionado para a plataforma de pagamento da InfinitePay.
            </p>
            --}}

            <div class="text-sm text-gray-700 space-y-2">
                <p class="font-semibold text-blue-800">Como funciona o pagamento</p>
                <p>
                    Os pagamentos são processados pelo <strong>Mercado Pago</strong>.
                    Após a realização do pagamento pode levar alguns minutos para que
                    a confirmação seja recebida em nosso sistema.
                </p>
                <p>
                    <strong>Pagamentos via Pix são creditados imediatamente</strong>,
                    porém a notificação de confirmação pode levar alguns minutos para
                    aparecer na plataforma.
                </p>
            </div>
        </div>
    </div>

    <style>
        .credito-card { transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease; position: relative; }
        .credito-opcao:hover .credito-card { border-color: #84e1bc; transform: translateY(-2px); }
        .credito-opcao input:checked + .credito-card {
            border-color: #057a55; background-color: #f3faf7;
            box-shadow: 0 10px 15px -3px rgba(5,122,85,.15), 0 4px 6px -2px rgba(5,122,85,.1);
        }
        .credito-opcao input:focus + .credito-card { box-shadow: 0 0 0 3px rgba(132,225,188,.45); }
        .credito-check { visibility: hidden; }
        .credito-opcao input:checked + .credito-card .credito-check { visibility: visible; }
        .credito-badge { position: absolute; top: -0.75rem; left: 50%; transform: translateX(-50%); white-space: nowrap; }
        .credito-btn:disabled { opacity: .5; cursor: not-allowed; }
    </style>

    @section('jquery')
    @endsection
</x-area-cliente-layout>
