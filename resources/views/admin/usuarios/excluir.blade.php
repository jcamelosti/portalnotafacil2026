<x-area-admin-layout title="Excluir Usuário">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Excluir Usuário') }}
        </h2>
    </x-slot>

    <div class="w-full px-4 py-6 sm:px-6 lg:px-8">
        @include('components.mensagens')

        <div class="bg-white shadow-xl rounded-lg border-t-4 border-red-600 mt-2">
            <div class="px-5 py-5">
                <h1 class="text-lg font-bold text-red-700">
                    Confirmar exclusão definitiva
                </h1>
                <p class="mt-2 text-sm text-gray-700">
                    Você está prestes a excluir o usuário
                    <strong>{{ $user->name }}</strong> ({{ $user->email }}).
                </p>
                <p class="mt-1 text-sm text-gray-700">
                    Esta ação <strong>não pode ser desfeita</strong>. Serão removidos permanentemente o usuário
                    e <strong>todos os registros relacionados</strong> a ele:
                </p>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-4">
                    @foreach($resumo as $rotulo => $total)
                        <div class="rounded border px-3 py-2 {{ $total > 0 ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-gray-50' }}">
                            <div class="text-xl font-bold {{ $total > 0 ? 'text-red-700' : 'text-gray-400' }}">{{ $total }}</div>
                            <div class="text-xs text-gray-600 uppercase">{{ $rotulo }}</div>
                        </div>
                    @endforeach
                </div>

                <p class="mt-4 text-xs text-gray-500">
                    Também serão removidos: atividades, CNAEs, NBS, correlações tributárias, tributações, consultas DF-e,
                    logs e validações das empresas próprias; itens de fatura e serviços; créditos, tokens de API, times
                    e sessões do usuário; arquivos de certificado e foto de perfil.
                    Empresas de outros usuários compartilhadas com ele <strong>não</strong> são excluídas — apenas o vínculo.
                </p>

                <form method="POST" action="{{ route('admin.usuarios.destroy', $user->id) }}" class="mt-6"
                      x-data="{ digitado: '', email: {{ json_encode(mb_strtolower($user->email)) }} }">
                    @csrf
                    @method('DELETE')

                    <label class="block text-sm">
                        <span class="text-gray-700">
                            Para confirmar, digite o e-mail do usuário: <strong class="select-all">{{ $user->email }}</strong>
                        </span>
                        <input type="text" name="confirmacao" x-model="digitado" autocomplete="off" required
                               class="block w-full md:w-1/2 mt-1 text-sm form-input rounded border-gray-300 focus:border-red-400 focus:outline-none">
                        @error('confirmacao')
                            <span class="text-xs text-red-600"><strong>{{ $message }}</strong></span>
                        @enderror
                    </label>

                    <div class="flex flex-wrap gap-2 justify-end mt-6">
                        <a href="{{ route('admin.usuarios.index') }}"
                           class="px-4 py-2 border border-gray-300 bg-white text-sm rounded hover:text-gray-500">
                            Cancelar
                        </a>
                        <button type="submit"
                                :disabled="digitado.trim().toLowerCase() !== email"
                                :class="{ 'opacity-50 cursor-not-allowed': digitado.trim().toLowerCase() !== email }"
                                onclick="return confirm('Excluir definitivamente o usuário e todos os seus registros?');"
                                class="px-4 py-2 rounded-md border border-transparent bg-red-600 text-sm font-medium text-white shadow-sm hover:bg-red-500">
                            Excluir definitivamente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-area-admin-layout>
