<x-area-cliente-layout title="Empresas">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Empresas') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto w-full sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Empresas
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Listagem de Solicitações de Compartilhamento de Empresas
                </p>
            </h1>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                    <a href="{{ route('empresas.nova-solicitar-acesso-empresa') }}"
                        class="inline-flex justify-center w-full rounded-md border border-blue-700 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                    Solicitar acesso a Empresa
                    </a>
                </span>
            </div>

           <!-- listagem da pesquisa -->

        </div>

        @include('components.mensagens')

        <div class="container-list">
            @forelse ($solicitacoes as $item)
                <div
                    class="
                                flex flex-col
                                items-center
                                p-2
                                {{ ($item->autorizado == 'S') ? 'bg-green-100': 'bg-red-100' }}
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
                        
                            @if(!is_null($item->empresa->razao_social))
                                {{ $item->empresa->razao_social }}
                            @elseif(!is_null($item->empresa->nome_fantasia))
                                {{ $item->empresa->nome_fantasia }} 
                            @else
                                {{ $item->empresa->cpf_cnpj }}
                            @endif

                                <span
                                class="
                                rounded rounded-lg
                                bg-green-400
                                text-green-50 text-xs
                                px-2
                                ml-1
                                uppercase
                                ">Usuário Responsável: {{$item->responsavel->name}}</span>

                        </div>
                        <div class="px-6 py-4 text-sm font-medium leading-5 text-right whitespace-nowrap">
                            <!--a class="btn-crud-show" href="{ { route('empresas.edit', $item->id) } }">
                                Editar
                            </a-->
                        </div>
                    </div>
                    <div class="flex flex-row items-start justify-between w-full">
                        <div class="flex inline-block">
                            <span
                                class="
                                            rounded rounded-lg
                                            bg-yellow-400
                                            text-yellow-50 text-xs
                                            px-2
                                            ml-1
                                            uppercase
                                            font-bold
                                        ">{{ $item->cpf_cnpj }}</span>
                        </div>
                    </div>
                </div>
            @empty
                Não há informações para Exibir
            @endforelse
        </div>
    </div>

    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        {!! $solicitacoes->links('pagination') !!}
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
