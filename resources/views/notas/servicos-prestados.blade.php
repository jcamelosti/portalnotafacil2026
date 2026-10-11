<x-area-empresa-layout title="Notas Emitidas">
    <div class="py-10 mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Notas Fiscais de Serviço
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Consultar Serviços Prestador por Período
                </p>
            </h1>
        </div>

        
        <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                @include('components.mensagens')

                <form method="POST" action="{{ route('nfse.consultar-servicos-prestados') }}"
                    class="bg-white p-4 rounded-lg shadow mb-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-4">
                        <!-- Linha de baixo -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                            <!-- Data Inicial -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Data Inicial
                                </label>
                                <input type="date" name="data_inicio"
                                    value="{{ request('data_inicio') ?? $data['data_inicio'] }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Data Final -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Data Final
                                </label>
                                <input type="date" name="data_fim"
                                    value="{{ request('data_fim') ?? $data['data_fim'] }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Botões -->
                            <div class="md:col-span-2 flex gap-2">
                                <button type="submit"
                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition">
                                    🔍 Buscar Notas
                                </button>

                                <a href="{{ route('nota.index') }}"
                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md text-center transition">
                                    Limpar
                                </a>
                            </div>

                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div>
</x-area-empresa-layout>
