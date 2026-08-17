<x-area-empresa-layout title="Tomadores">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Tomadores') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto w-full sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Tomadores
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Listagem
                </p>
            </h1>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                    <a href="{{ route('tomadores.create_ext') }}"
                        class="inline-flex justify-center w-full rounded-md border border-blue-700 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                    Adicionar Tomador(Exterior)
                    </a>
                </span>
                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                    <a href="{{ route('tomadores.create') }}"
                        class="inline-flex justify-center w-full rounded-md border border-blue-700 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                    Adicionar
                    </a>
                </span>
            </div>

            <div class="px-4 py-3 mb-8 bg-white ">
                {!! Form::open(['route'=>'tomadores.index', 'method'=>'GET']) !!}
                <label class="block text-sm">
                    <span class="text-gray-700 ">Tomadores:</span>
                    {!! Form::select('tomador_id', $tomadoresList, isset($pesquisa['tomador_id']) ? $pesquisa['tomador_id'] : null, ['class'=>'select2 block w-full mt-1 text-sm    form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                    @if ($errors->has('tomador_id'))
                        <span class="text-xs text-red-600 ">
                            <strong>{{ $errors->first('tomador_id') }}</strong>
                        </span>
                    @endif
                </label>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                        <button type="submit" type="button"
                                class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                        Pesquisar
                      </button>
                    </span>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        @include('components.mensagens')

        <div class="container-list">
            @forelse ($tomadores as $item)
                <div class="
                                flex flex-col
                                items-center
                                p-2
                                bg-gray-50
                                shadow-md
                                m-1
                                w-full
                            "
                >
                    <div class="flex flex-row items-center justify-between w-full">
                        <div
                            class="
                                        flex
                                        items-center
                                        flex-grow
                                        font-bold
                                        text-base
                                        md:text-2xl
                                    "
                        >
                            {{$item->razao_social}}
{{--                            <span--}}
{{--                                class="--}}
{{--                                            rounded rounded-lg--}}
{{--                                            bg-green-400--}}
{{--                                            text-green-50 text-xs--}}
{{--                                            px-2--}}
{{--                                            ml-1--}}
{{--                                            uppercase--}}
{{--                                        "--}}
{{--                            >Vendido</span>--}}
{{--                            <span--}}
{{--                                class="--}}
{{--                                            rounded rounded-lg--}}
{{--                                            bg-blue-400--}}
{{--                                            text-blue-50 text-xs--}}
{{--                                            px-2--}}
{{--                                            ml-1--}}
{{--                                            uppercase--}}
{{--                                        "--}}
{{--                            >Disponível</span>--}}
{{--                            <span--}}
{{--                                class="--}}
{{--                                            rounded rounded-lg--}}
{{--                                            bg-red-400--}}
{{--                                            text-red-50 text-xs--}}
{{--                                            px-2--}}
{{--                                            ml-1--}}
{{--                                            uppercase--}}
{{--                                        "--}}
{{--                            >Devolvido</span>--}}
                        </div>
                        <div class="flex text-right text-green-500 hover:text-yellow-500">
                            <a class="btn-crud-show" href="{{ route('tomadores.edit', $item->id) }}">
                                <svg
                                    class="w-10 h-10"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                    ></path>
                                </svg>
                            </a>
                        </div>
{{--                        <div class="flex text-right text-red-500 hover:text-red-600">--}}
{{--                            <a class="btn-crud-show" href="#">--}}
{{--                                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">--}}
{{--                                    <path fill-rule="evenodd"--}}
{{--                                          d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"--}}
{{--                                          clip-rule="evenodd"></path>--}}
{{--                                </svg>--}}
{{--                            </a>--}}
{{--                        </div>--}}
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
                                        "
                            >{{$item->cpf_cnpj}}</span>
                        </div>
                    </div>
                </div>
            @empty
                Não há informações para Exibir
            @endforelse
        </div>
    </div>

    <div class="py-10 mx-auto w-full sm:px-6 lg:px-8">
        {!! $tomadores->links('pagination') !!}
    </div>

</x-area-empresa-layout>
