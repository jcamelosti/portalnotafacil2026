<x-area-admin-layout title="Notas Emitidas">
    <div class="py-10 mx-auto w-full sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Empresas Por Cidade/Estado
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Listagem de Empresas
                </p>
            </h1>
        </div>

        <table class="w-full whitespace-no-wrap mt-8">
            <thead>
            <tr
                class="items-center text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                <th class="px-4 py-3">Município</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3">Quantidade de Empresas</th>
                <th class="px-4 py-3">Última Emissão</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y">
            @foreach($resultado as $key => $lista)
                <tr class="items-center text-gray-700">
                    <td class="px-4 py-3">
                        <div>
                            <p class="text-xs text-gray-600">
                                {{ $lista->municipio }}
                            </p>
                        </div>
                    </td>

                    <td class="px-4 py-3">
                        <div>
                            <p class="text-xs text-gray-600">
                                {{ $lista->sigla }}
                            </p>
                        </div>
                    </td>

                    <td class="px-4 py-3 text-sm">
                        {{ $lista->total }}
                    </td>

                    <td class="px-4 py-3">
                        <div>
                            <p class="text-xs text-gray-600">
                                {{ \Carbon\Carbon::parse($lista->ultima_emissao)->format('d/m/Y H:m:s') }}
                            </p>
                        </div>
                    </td>
                </tr>
            @endforeach
                <tr class="items-center text-gray-700 bg-gray-400">
                    <td class="px-4 py-3">
                        <div>
                            <p class="text-x">
                                
                            </p>
                        </div>
                    </td>

                     <td class="px-4 py-3">
                        <div>
                            <p class="text-sm text-white">
                                Total Geral:
                            </p>
                        </div>
                    </td>
                    
                    <td class="px-4 py-3 text-sm text-white">
                        {{ $totalGeral }}
                    </td>

                    <td class="px-4 py-3 text-sm text-white">
                        
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</x-area-admin-layout>
