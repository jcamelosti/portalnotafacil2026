<x-area-cliente-layout title="Cobranças">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Faturas') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto w-full sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Faturas
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Listagem de Faturas de Cobranças
                </p>
            </h1>
        </div>

        @include('components.mensagens')

        <div class="container-list mt-2 w-full">
            <div class="px-4 py-4 mb-8 bg-white rounded-lg shadow-md ">
                
                <div class="flex mb-4">
                    <div class="w-full bg-gray-300 p-2">Dados da Fatura</div>
                </div>
                    
                <!-- Two columns -->
                <div class="flex mb-4">
                    <div class="w-1/2 bg-gray-200 p-2">Núm. Doc:</div>
                    <div class="w-1/2 bg-gray-200 p-2">{{$fatura->num_doc}}</div>
                </div>
                <div class="flex mb-4">
                    <div class="w-1/2 bg-gray-200 p-2">ID Transação:</div>
                    <div class="w-1/2 bg-gray-200 p-2">{{$fatura->transacao_id}}</div>
                </div>
                @if($fatura->items()->count() == 0)
                <div class="flex mb-4">
                    <div class="w-1/2 bg-gray-200 p-2">Operação:</div>
                    <div class="w-1/2 bg-gray-200 p-2">Crédito em Conta</div>
                </div>
                @else
                <div class="flex mb-4">
                    <div class="w-1/2 bg-gray-200 p-2">Operação:</div>
                    <div class="w-1/2 bg-gray-200 p-2">Liberação de Licença</div>
                </div>
                @endif
                <div class="flex mb-4">
                    <div class="w-1/2 bg-gray-200 p-2">Nome Faturamento - Cliente:</div>
                    <div class="w-1/2 bg-gray-200 p-2">{{ empty($fatura->user->cliente->razao_social) ? $fatura->user->name : $fatura->user->cliente->razao_social}}</div>
                </div>
                @if($fatura->items()->count() > 0)
                <div class="flex mb-4">
                    <div class="w-1/2 bg-gray-200 p-2">Empresa:</div>
                    <div class="w-1/2 bg-gray-200 p-2">{{$fatura->empresa->razao_social}}</div>
                </div>
                @endif
                <div class="flex mb-4">
                    <div class="w-1/2 bg-gray-200 p-2">Data Criação Fatura:</div>
                    <div class="w-1/2 bg-gray-200 p-2">{{$fatura->created_at->format('d/m/Y H:i:s')}}</div>
                </div>
                <div class="flex mb-4">
                    <div class="w-1/2 bg-gray-200 p-2">Data Alteração Status:</div>
                    <div class="w-1/2 bg-gray-200 p-2">{{$fatura->updated_at->format('d/m/Y H:i:s')}}</div>
                </div>

                <div class="flex mb-4">
                    <div class="w-1/2 bg-gray-200 p-2">Status da Fatura:</div>
                    <div class="w-1/2 bg-gray-200 p-2">
                        @if($fatura->fatura_status_id == 3)
                                <span
                                class="
                                rounded rounded-lg
                                bg-green-400
                                text-green-50 text-xs
                                px-2
                                ml-1
                                p-2
                                uppercase
                                ">{{$fatura->statusFatura->nome}}</span>
                            @elseif($fatura->fatura_status_id == 1)
                                <span
                                    class="
                                    rounded rounded-lg
                                    bg-yellow-400
                                    text-yellow-50 text-xs
                                    px-2
                                    ml-1
                                    p-2
                                    uppercase
                                    ">{{$fatura->statusFatura->nome}}</span>
                            @else 
                                <span 
                                class=" 
                                rounded rounded-lg 
                                bg-red-400
                                text-red-50 text-xs
                                px-2
                                ml-1
                                p-2
                                uppercase
                                ">{{$fatura->statusFatura->nome}}</span>
                            @endif
                    </div>
                </div>
                <div class="flex mb-4">
                    <div class="w-1/2 bg-gray-200 p-2">Chave NFSe Nacional:</div>
                    <div class="w-1/2 bg-gray-200 p-2">
                        {{$fatura->servico->chave_nfse ?? 'NFS-e não Emitida'}}
                        @if(!empty($fatura->servico->chave_nfse))
                            <a href="https://www.nfse.gov.br/consultapublica" class="px-3 py-1 text-xs font-semibold text-white bg-green-600 rounded hover:bg-green-700">Download da NFS-e</a>   
                        @endif
                    </div>
                </div>                    
                <!-- Three columns -->
                <!--div class="flex mb-4">
                    <div class="w-1/3 bg-gray-400 h-12"></div>
                    <div class="w-1/3 bg-gray-500 h-12"></div>
                    <div class="w-1/3 bg-gray-400 h-12"></div>
                </div-->
                
                <!-- Four columns -->
                <!--div class="flex mb-4">
                    <div class="w-1/4 bg-gray-500 h-12"></div>
                    <div class="w-1/4 bg-gray-400 h-12"></div>
                    <div class="w-1/4 bg-gray-500 h-12"></div>
                    <div class="w-1/4 bg-gray-400 h-12"></div>
                </div-->
                    
                <!-- Five columns -->
                <!--div class="flex mb-4">
                    <div class="w-1/5 bg-gray-500 h-12">asdf</div>
                    <div class="w-1/5 bg-gray-400 h-12">asdf</div>
                    <div class="w-1/5 bg-gray-500 h-12">asdf</div>
                    <div class="w-1/5 bg-gray-400 h-12">asf</div>
                    <div class="w-1/5 bg-gray-500 h-12">asdf</div>
                </div-->
                    
                <!-- Six columns -->
                <!--div class="flex">
                    <div class="w-1/6 bg-gray-400 h-12"></div>
                    <div class="w-1/6 bg-gray-500 h-12"></div>
                    <div class="w-1/6 bg-gray-400 h-12"></div>
                    <div class="w-1/6 bg-gray-500 h-12"></div>
                    <div class="w-1/6 bg-gray-400 h-12"></div>
                    <div class="w-1/6 bg-gray-500 h-12"></div>
                </div-->
        

                @if($fatura->items()->count() > 0)
                    <div class="flex flex-wrap -mx-1 border-b py-2 mt-6 items-start">
                        <div class="flex-1 px-1">
                            <p class="text-gray-600 uppercase tracking-wide text-xs font-bold">Descrição</p>
                        </div>

                        <div class="px-1 w-32 text-right">
                            <p class="text-gray-600 uppercase tracking-wide text-xs font-bold">Qtd.</p>
                        </div>

                        <div class="px-1 w-32 text-right">
                            <p class="leading-none">
                                <span class="block uppercase tracking-wide text-xs font-bold text-gray-600">Valor Unit.</span>
                                <span class="font-medium text-xs text-gray-500">(R$)</span>
                            </p>
                        </div>

                        <div class="px-1 w-32 text-right">
                            <p class="leading-none">
                                <span class="block uppercase tracking-wide text-xs font-bold text-gray-600">Total</span>
                                <span class="font-medium text-xs text-gray-500">(R$)</span>
                            </p>
                        </div>
                    </div>
                    @foreach($fatura->items as $fatItem)
                        <div class="flex flex-wrap -mx-1 border-b py-2 items-start">
                            <div class="flex-1 px-1">
                                <p class="text-gray-600 uppercase tracking-wide text-xs font-bold">{{$fatItem->plano->plano_nome}} - {{$fatItem->variacaoPlano->descricao}}</p>
                            </div>

                            <div class="px-1 w-32 text-right">
                                <p class="text-gray-600 uppercase tracking-wide text-xs font-bold">{{$fatItem->qtd}}</p>
                            </div>

                            <div class="px-1 w-32 text-right">
                                <p class="leading-none">
                                    <span class="block uppercase tracking-wide text-xs font-bold text-gray-600">{{ number_format($fatItem->valor, 2,',', '.') }}</span>
                                    <span class="font-medium text-xs text-gray-500">(R$)</span>
                                </p>
                            </div>

                            <div class="px-1 w-32 text-right">
                                <p class="leading-none">
                                    <span class="block uppercase tracking-wide text-xs font-bold text-gray-600">{{ number_format( ($fatItem->valor * $fatItem->qtd), 2,',', '.')}}</span>
                                    <span class="font-medium text-xs text-gray-500">(R$)</span>
                                </p>
                            </div>
                        </div>
                    @endforeach
                @endif

                <div class="py-2 ml-auto mt-20" style="width: 320px">
                    <!--div class="flex justify-between mb-3">
                        <div class="text-gray-800 text-right flex-1">Total incl. GST</div>
                        <div class="text-right w-40">
                            <div class="text-gray-800 font-medium" x-html="netTotal">0</div>
                        </div>
                    </div-->
                    <!--div class="flex justify-between mb-4">
                        <div class="text-sm text-gray-600 text-right flex-1">GST(18%) incl. in Total</div>
                        <div class="text-right w-40">
                            <div class="text-sm text-gray-600" x-html="totalGST">0</div>
                        </div>
                    </div-->
                    <div class="py-2 border-t border-b">
                        <div class="flex justify-between">
                            <div class="text-xl text-gray-600 text-right flex-1">Total da Fatura</div>
                            <div class="text-right w-40">
                                <div class="text-xl text-gray-800 font-bold">R$ {{ number_format($fatura->total,2,',', '.')}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .top-100 {
                top: 100%
            }

            .bottom-100 {
                bottom: 100%
            }

            .max-h-select {
                max-height: 300px;
            }
        </style>
        @section('jquery')
        @endsection
</x-area-cliente-layout>
    