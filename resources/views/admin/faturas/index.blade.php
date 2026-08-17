<x-area-admin-layout title="Cobranças">
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

        <div class="grid gap-6 mb-8 mt-10 md:grid-cols-2 xl:grid-cols-4">
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs ">
                <!--div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full  ">
                    0
                </div-->
                <div>
                    <p class="mb-2 text-sm font-medium text-green-600 ">
                        Recebimentos do Mês
                    </p>
                    <p class="text-lg font-semibold text-green-700 ">
                        R$ {{ number_format($totais['total_mes'], 2, ',', '') }}
                    </p>
                </div>
            </div>         
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs ">
                <!--div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full  ">
                    0
                </div-->
                <div>
                    <p class="mb-2 text-sm font-medium text-green-600 ">
                        Recebimentos do Dia - {{ date('d/m/y')}}
                    </p>
                    <p class="text-lg font-semibold text-green-700 ">
                        R$ {{ number_format($totais['total_dia'], 2, ',', '') }}
                    </p>
                </div>
            </div>     
            {{--@if(date('N', strtotime(date('Y-m-d'))) < 5)    
                <div class="flex items-center p-4 bg-white rounded-lg shadow-xs ">
                    <!--div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full  ">
                        0
                    </div-->
                    <div>
                        <p class="mb-2 text-sm font-medium text-blue-600 ">
                            Crédito em Conta para Hoje
                        </p>
                        <p class="text-lg font-semibold text-blue-700 ">
                            R$ {{ number_format($totais['total_credito_conta_hoje'], 2, ',', '') }}
                        </p>
                    </div>
                </div>
                   
                <div class="flex items-center p-4 bg-white rounded-lg shadow-xs ">
                    <!--div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full  ">
                    0
                    </div-->
                    <div>
                        <p class="mb-2 text-sm font-medium text-red-600 ">
                            Crédito em Conta para Amanhã
                        </p>
                        <p class="text-lg font-semibold text-red-700 ">
                            R$ {{ number_format($totais['total_credito_conta_amanha'], 2, ',', '') }}
                        </p>
                    </div>
                </div>         
            @else
                <div class="flex items-center p-4 bg-white rounded-lg shadow-xs ">
                    <!--div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full  ">
                    0
                    </div-->
                    <div>
                        <p class="mb-2 text-sm font-medium text-red-600 ">
                            Crédito em Conta para Segunda-Feira - {{ $proximoDiaUtil }}
                        </p>
                        <p class="text-lg font-semibold text-red-700 ">
                            R$ {{ number_format( ($totais['total_credito_conta_hoje'] + $totais['total_credito_conta_amanha']) - $totais['total_credito_conta_amanha'] , 2, ',', '') }}
                        </p>
                    </div>
                </div>         
            @endif--}}
        </div> 

        <div class="container-list">
            @forelse ($faturas as $item)
                <div
                    class="
                                flex flex-col
                                items-center
                                p-2
                                bg-gray-50
                                shadow-md
                                m-1
                                w-full
                            ">
                    <div class="flex flex-row items-center justify-between w-full">
                        <div
                            class="
                                        flex
                                        items-center
                                        flex-grow
                                        font-bold
                                        text-base
                                        md:text-2xl
                                    ">
                            {{ $item->user->name }}
                            @if($item->fatura_status_id == 3)
                                <span
                                class="
                                rounded rounded-lg
                                bg-green-400
                                text-green-50 text-xs
                                px-2
                                ml-1
                                uppercase
                                ">{{$item->statusFatura->nome}}</span>
                            @elseif($item->fatura_status_id == 1)
                                <span
                                    class="
                                    rounded rounded-lg
                                    bg-yellow-400
                                    text-yellow-50 text-xs
                                    px-2
                                    ml-1
                                    uppercase
                                    ">{{$item->statusFatura->nome}}</span>
                            @else 
                                <span 
                                class=" 
                                rounded rounded-lg 
                                bg-red-400
                                text-red-50 text-xs
                                px-2
                                ml-1
                                uppercase
                                ">{{$item->statusFatura->nome}}</span>
                            @endif

                            
                            <span 
                                class=" 
                                rounded rounded-lg 
                                {{ empty($item->servico->chave_nfse) ? 'bg-teal-400' : 'bg-green-700'}}
                                text-teal-50 text-xs
                                px-2
                                ml-1
                                uppercase
                                ">Chave NFSe: {{ $item->servico->chave_nfse ?? 'Nota Não Emitida'}}</span>
                            
                        </div>
                        <div class="px-6 py-6 text-sm flex justify-content align-center font-medium leading-5 text-right whitespace-nowrap">
                            <a class="btn-crud-show  
                                            rounded rounded-lg
                                            bg-blue-400
                                            text-blue-50 text-xs
                                            px-4
                                            py-2
                                            uppercase
                                            font-bold
                                        " href="{{ route('admin.faturas.show', $item->id) }}">
                                Visualizar
                            </a>

                            @if(empty($item->servico->id))
                            <a class="btn-crud-show  
                                            rounded rounded-lg
                                            bg-green-400
                                            text-green-50 text-xs
                                            px-4
                                            py-2
                                            uppercase
                                            font-bold ml-2
                                        " href="{{ route('admin.faturas.gerar-servico', $item->id) }}">
                                Emitir NFSe
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-row items-start justify-between w-full">
                        <div class="flex inline-block">
                            <span
                                class="
                                            rounded rounded-lg
                                            bg-orange-400
                                            text-white text-xs
                                            px-2
                                            ml-1
                                            uppercase
                                            font-bold
                                        ">Nome Faturamento: {{ empty($item ->user->cliente->razao_social) ? $item->user->name : $item->user->cliente->razao_social }}</span>
                            <span
                                class="
                                            rounded rounded-lg
                                            bg-blue-400
                                            text-blue-50 text-xs
                                            px-2
                                            ml-1
                                            uppercase
                                            font-bold
                                        ">Núm. Doc: {{ $item->num_doc }}</span>
                            <span
                            class="
                                        rounded rounded-lg
                                        bg-purple-900
                                        text-purple-50 text-xs
                                        px-2
                                        ml-1
                                        uppercase
                                        font-bold
                                    ">Valor: R$ {{ number_format($item->total,2,',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                Não há informações para Exibir
            @endforelse
        </div>
    </div>

    <div class="py-0 mx-auto w-full sm:px-6 lg:px-8">
        {!! $faturas->links('pagination') !!}
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
</x-area-admin-layout>
