{{-- Espera: $titulo, $registros, $linkRemoverDados (bool) --}}
<div class="w-full mt-6 overflow-hidden rounded-lg shadow-xs bg-white">
    <h3 class="px-5 py-4 text-lg font-medium text-gray-900 border-b">
        {{ $titulo }}
        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">{{ $registros->count() }}</span>
    </h3>
    <div class="w-full overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                    <th class="px-4 py-3">Seq.</th>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Razão Social</th>
                    <th class="px-4 py-3">CPF/CNPJ</th>
                    <th class="px-4 py-3">Usuário Responsável</th>
                    <th class="px-4 py-3">Plano</th>
                    <th class="px-4 py-3">Validade Licença</th>
                    <th class="px-4 py-3">Dt. Últ. Emissão</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                @forelse ($registros as $idx => $empresa)
                    @php
                        $vencida = \Carbon\Carbon::parse($empresa->validate_licenca)->isPast();
                    @endphp
                    <tr class="text-sm text-gray-700 {{ $vencida ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-3">{{ $idx + 1 }}</td>
                        <td class="px-4 py-3">
                            @if ($linkRemoverDados)
                                <a href="{{ route('admin.empresas.remover-dados', $empresa->id) }}" class="text-blue-700 hover:underline"
                                   title="Remover dados da empresa">
                                    {{ $empresa->id }}
                                </a>
                            @else
                                {{ $empresa->id }}
                            @endif
                        </td>
                        <td class="px-4 py-3 font-semibold">{{ Str::limit($empresa->razao_social, 30, '...') }}</td>
                        <td class="px-4 py-3">{{ $empresa->cpf_cnpj }}</td>
                        <td class="px-4 py-3">{{ $empresa->name }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">{{ $empresa->plano_nome }}</span>
                        </td>
                        <td class="px-4 py-3 {{ $vencida ? 'text-red-700 font-semibold' : '' }}">
                            {{ \Carbon\Carbon::parse($empresa->validate_licenca)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $empresa->ultima_emissao ? \Carbon\Carbon::parse($empresa->ultima_emissao)->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.licencas.edit', $empresa->id) }}"
                               class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700">
                                Editar Licença
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">
                            Nenhum registro encontrado
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
