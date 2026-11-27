<x-area-admin-layout title="Novo Usuário">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Novo Usuário') }}
        </h2>
    </x-slot>

    <div class="w-full px-4 py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-lg py-2">
            <h1 class="px-4 py-3">
                Novo Usuário
                <p class="mt-1 text-sm text-gray-500">Cadastro de usuário do portal</p>
            </h1>
        </div>

        @include('components.mensagens')

        <div class="bg-white rounded-lg shadow-md px-5 py-5 mt-6">
            <form method="POST" action="{{ route('admin.usuarios.store') }}">
                @csrf
                @include('admin.usuarios._form')
            </form>
        </div>
    </div>
</x-area-admin-layout>
