<x-area-admin-layout title="Empresas">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Usuários') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto w-full sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Usuários
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Listagem
                </p>
            </h1>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                    <a href="{{ route('admin.usuarios.create') }}"
                        class="inline-flex justify-center w-full rounded-md border border-blue-700 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                        Adicionar
                    </a>
                </span>
            </div>

            <div class="px-4 py-3 mb-8 bg-white ">
                {!! Form::open(['route' => 'admin.usuarios.index', 'method' => 'GET']) !!}
                <label class="block text-sm">
                    <span class="text-gray-700 ">Usuários:</span>
                    {!! Form::select('user_id', $usersList, isset($pesquisa['user_id']) ? $pesquisa['user_id'] : null, ['class' => 'select2  block w-full mt-1 text-sm    form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                    @if ($errors->has('user_id'))
                        <span class="text-xs text-red-600 ">
                            <strong>{{ $errors->first('user_id') }}</strong>
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
            @forelse ($users as $item)
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
                            {{ $item->name }}
                        </div>
                        <div class="px-6 py-4 text-sm font-medium leading-5 whitespace-nowrap flex items-center gap-3">
    
                            <a class="inline-flex rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                            href="{{ route('admin.usuarios.edit', $item->id) }}">
                                Editar
                            </a>

                            <a class="inline-flex rounded-md border border-transparent px-4 py-2 bg-yellow-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-yellow-500 focus:outline-none transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                            href="{{ route('admin.usuarios.show', $item->id) }}">
                                Visualizar
                            </a>

                            <form id="delete-form" method="POST" action="{{ route('admin.usuarios.destroy', $item->id) }}">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <input type="submit"
                                    class="inline-flex rounded-md border border-transparent px-4 py-2 bg-red-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-red-500 focus:outline-none transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                                    value="Remover Registro">
                            </form>
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
                                        ">{{ $item->email }}</span>
                        </div>
                    </div>
                </div>
            @empty
                Não há informações para Exibir
            @endforelse
        </div>
    </div>

    <div class="py-0 mx-auto w-full sm:px-6 lg:px-8">
        {!! $users->links('pagination') !!}
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
