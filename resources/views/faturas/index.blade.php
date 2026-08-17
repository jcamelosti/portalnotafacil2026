<x-area-cliente-layout title="Cobranças">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Cobranças') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto w-full sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Faturas
                <p class="mt-1 w-full text-sm text-gray-500">
                    Faturas referentes aos serviços prestados pela {{ config('app.name') }}. 
                    Clique em "Visualizar" para acessar os detalhes de cada fatura, incluindo o status de pagamento e a chave de acesso/Download da NFS-e, quando disponível.
                </p>
            </h1>
        </div>

        @include('components.mensagens')
        
        <div class="px-4 py-3 mb-8 bg-white ">
            {!! Form::open(['route'=>'faturas.index', 'method'=>'GET']) !!}
            <label class="block text-sm">
                <span class="text-gray-700 ">Empresa:</span>
                {!! Form::select('empresa_id', $empresasList, isset($pesquisa['empresa_id']) ? $pesquisa['empresa_id'] : null, ['class'=>'select2 block w-full mt-1 text-sm    form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                @if ($errors->has('empresa_id'))
                    <span class="text-xs text-red-600 ">
                        <strong>{{ $errors->first('empresa_id') }}</strong>
                    </span>
                @endif
            </label>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                    <button type="submit" type="button"
                            class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                    Selecionar
                    </button>
                </span>
            </div>
            {!! Form::close() !!}
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
                                        " href="{{ route('faturas.show', $item->id) }}">
                                Visualizar
                            </a>
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
</x-area-cliente-layout>
