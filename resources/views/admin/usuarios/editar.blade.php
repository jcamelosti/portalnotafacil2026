<x-area-admin-layout title="Edição de Usuário">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Edição de Usuário') }}
        </h2>
    </x-slot>

    <div class="w-full px-4 py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-lg py-2">
            <h1 class="px-4 py-3">
                {{ $user->name }}
                <p class="mt-1 text-sm text-gray-500">
                    #{{ $user->id }} · cadastrado em {{ optional($user->created_at)->format('d/m/Y H:i') }}
                </p>
            </h1>

            <div class="flex flex-wrap gap-2 bg-gray-50 px-4 py-3 justify-end">
                <a href="{{ route('admin.usuarios.show', $user->id) }}"
                   class="px-4 py-2 border border-blue-700 bg-white text-sm rounded hover:text-gray-500">
                    Visualizar empresas
                </a>
                @if($user->id !== auth()->id())
                    <a href="{{ route('admin.usuarios.excluir', $user->id) }}"
                       class="px-4 py-2 rounded border border-transparent bg-red-600 text-sm text-white hover:bg-red-500">
                        Excluir usuário
                    </a>
                @endif
            </div>
        </div>

        @include('components.mensagens')

        <div class="bg-white rounded-lg shadow-md px-5 py-5 mt-6">
            <form method="POST" action="{{ route('admin.usuarios.update', $user->id) }}">
                @csrf
                @method('PUT')
                @include('admin.usuarios._form')
            </form>
        </div>
    </div>
</x-area-admin-layout>
