<x-area-admin-layout title="Usuários">
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
                    Listagem — {{ $users->total() }} usuário(s) encontrado(s)
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

            <div class="px-4 py-3 mb-8 bg-white">
                {!! Form::open(['route' => 'admin.usuarios.index', 'method' => 'GET']) !!}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="block text-sm">
                        <span class="text-gray-700">Usuário:</span>
                        {!! Form::select('user_id', $usersList, $pesquisa['user_id'] ?? null, ['class' => 'select2 block w-full mt-1 text-sm form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple']) !!}
                    </label>

                    <label class="block text-sm">
                        <span class="text-gray-700">Nome ou e-mail:</span>
                        <input type="text" name="busca" value="{{ $pesquisa['busca'] ?? '' }}" placeholder="Digite parte do nome ou e-mail"
                               class="block w-full mt-1 text-sm form-input rounded border-gray-300 focus:border-purple-400 focus:outline-none">
                    </label>

                    <label class="block text-sm">
                        <span class="text-gray-700">Perfil:</span>
                        {!! Form::select('perfil', ['' => 'Todos', '1' => 'Administradores', '0' => 'Clientes'], $pesquisa['perfil'] ?? '', ['class' => 'block w-full mt-1 text-sm form-select focus:border-purple-400 focus:outline-none']) !!}
                    </label>
                </div>
                <div class="bg-gray-50 px-4 py-3 mt-4 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                        <button type="submit"
                            class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            Pesquisar
                        </button>
                    </span>
                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                        <a href="{{ route('admin.usuarios.index') }}"
                           class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:text-gray-500">
                            Limpar
                        </a>
                    </span>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        @include('components.mensagens')

        <div class="container-list">
            @forelse ($users as $item)
                <div class="flex flex-col items-center p-2 bg-gray-50 shadow-md m-1 w-full">
                    <div class="flex flex-col md:flex-row md:items-center justify-between w-full">
                        <div class="flex items-center flex-grow gap-3 font-bold text-base md:text-2xl">
                            <div class="w-10 h-10 flex-shrink-0 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
                                {{ mb_strtoupper(mb_substr($item->name, 0, 1)) }}
                            </div>
                            <div>
                                {{ $item->name }}
                                <div class="text-xs font-normal text-gray-500">
                                    #{{ $item->id }} · cadastrado em {{ optional($item->created_at)->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                        <div class="px-2 md:px-6 py-4 text-sm font-medium leading-5 whitespace-nowrap flex flex-wrap items-center gap-3">
                            <a class="inline-flex rounded-md border border-transparent px-4 py-2 bg-green-600 text-sm leading-5 font-medium text-white shadow-sm hover:bg-green-500"
                               href="{{ route('admin.usuarios.edit', $item->id) }}">
                                Editar
                            </a>

                            <a class="inline-flex rounded-md border border-transparent px-4 py-2 bg-yellow-600 text-sm leading-5 font-medium text-white shadow-sm hover:bg-yellow-500"
                               href="{{ route('admin.usuarios.show', $item->id) }}">
                                Visualizar
                            </a>

                            @if($item->id !== auth()->id())
                                <a class="inline-flex rounded-md border border-transparent px-4 py-2 bg-red-600 text-sm leading-5 font-medium text-white shadow-sm hover:bg-red-500"
                                   href="{{ route('admin.usuarios.excluir', $item->id) }}">
                                    Excluir
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-row flex-wrap items-start gap-1 w-full">
                        <span class="rounded rounded-lg bg-yellow-400 text-yellow-50 text-xs px-2 ml-1 uppercase font-bold">{{ $item->email }}</span>

                        @if($item->is_admin == '1')
                            <span class="rounded rounded-lg bg-purple-600 text-white text-xs px-2 uppercase font-bold">Administrador</span>
                        @else
                            <span class="rounded rounded-lg bg-gray-400 text-white text-xs px-2 uppercase font-bold">Cliente</span>
                        @endif

                        @if($item->email_verified_at)
                            <span class="rounded rounded-lg bg-green-500 text-white text-xs px-2 uppercase font-bold">E-mail verificado</span>
                        @else
                            <span class="rounded rounded-lg bg-red-500 text-white text-xs px-2 uppercase font-bold">E-mail não verificado</span>
                        @endif

                        <span class="rounded rounded-lg bg-blue-500 text-white text-xs px-2 uppercase font-bold">
                            {{ $item->empresas_count }} empresa(s)
                            @if($item->empresas_compartilhadas_count)
                                + {{ $item->empresas_compartilhadas_count }} compartilhada(s)
                            @endif
                        </span>
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
</x-area-admin-layout>
