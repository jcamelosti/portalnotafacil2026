<x-area-admin-layout title="Empresa Atividades">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Empresa Atividades') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Empresa Atividades
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Listagem
                </p>
            </h1>

            <div class="grid grid-cols-4 gap-1 bg-gray-50 px-4 py-3 sm:px-6 mt-6 sm:flex sm:flex-row-reverse">
                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                    <a href="{{ route('admin.empresas.edit', $empresaId) }}"
                        class="inline-flex justify-center w-full rounded-md border border-blue-700 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                        Voltar
                    </a>
                </span>

                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                    <a href="{{ route('admin.empresa-atividades.create') }}"
                        class="inline-flex justify-center w-full rounded-md border border-blue-700 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                        Adicionar
                    </a>
                </span>
            </div>
        </div>

        @include('components.mensagens')

        <div class="container-list">
            @forelse ($atividades as $item)
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
                            {{ $item->descricao_atividade }}
                        </div>
                        <div class="px-6 py-4 text-sm font-medium leading-5 text-right whitespace-nowrap">
                            <a class="btn-crud-show" href="{{ route('admin.empresa-atividades.edit', $item->id) }}">
                                Editar
                            </a>
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
                                        ">{{ $item->codigo_atividade }}</span>
                        </div>
                    </div>
                </div>
            @empty
                Não há informações para Exibir
            @endforelse
        </div>
    </div>

    <div class="py-0 mx-auto w-full sm:px-6 lg:px-8">
        {!! $atividades->links('pagination') !!}
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
