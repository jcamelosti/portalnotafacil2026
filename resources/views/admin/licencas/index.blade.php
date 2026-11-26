<x-area-admin-layout title="Gestão de Licenças">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Gestão de Licenças') }}
        </h2>
    </x-slot>

    @php
        $situacaoAtual = $filtros['situacao'] ?? '';
        $cards = [
            'vencidas'    => ['label' => 'Vencidas',                          'valor' => (int) $contadores->vencidas,    'cor' => 'red'],
            'a_vencer'    => ['label' => "Vencem em até {$diasAVencer} dias", 'valor' => (int) $contadores->a_vencer,    'cor' => 'orange'],
            'ativas'      => ['label' => 'Ativas',                            'valor' => (int) $contadores->ativas,      'cor' => 'green'],
            'sem_licenca' => ['label' => 'Sem licença',                       'valor' => (int) $contadores->sem_licenca, 'cor' => 'gray'],
        ];
        $situacoes = ['' => 'Todas'] + collect($cards)->map(fn ($c) => $c['label'])->all();

        // Classes completas (literais) para não serem removidas pelo purge do Tailwind
        $estilos = [
            'red'    => ['card' => 'border-red-500 bg-red-50',       'titulo' => 'text-red-700',    'ring' => 'shadow-outline-red',    'badge' => 'bg-red-100 text-red-700',       'dot' => 'bg-red-500'],
            'orange' => ['card' => 'border-orange-500 bg-orange-50', 'titulo' => 'text-orange-700', 'ring' => 'shadow-outline-orange', 'badge' => 'bg-orange-100 text-orange-700', 'dot' => 'bg-orange-500'],
            'green'  => ['card' => 'border-green-500 bg-green-50',   'titulo' => 'text-green-700',  'ring' => 'shadow-outline-green',  'badge' => 'bg-green-100 text-green-700',   'dot' => 'bg-green-500'],
            'gray'   => ['card' => 'border-gray-500 bg-gray-50',     'titulo' => 'text-gray-700',   'ring' => 'shadow-outline-gray',   'badge' => 'bg-gray-200 text-gray-700',     'dot' => 'bg-gray-500'],
        ];
    @endphp

    <div class="w-full px-4 py-6 sm:px-6 lg:px-8">

        <div class="bg-white shadow-xl rounded-lg py-1">
            <h1 class="px-4 py-3">
                Gestão de Licenças
                <p class="mt-1 text-sm text-gray-500">
                    Consulte e altere a validade e o plano das licenças das empresas
                </p>
            </h1>

            {{-- Contadores / filtro rápido --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 px-4 pb-4">
                @foreach ($cards as $chave => $card)
                    <a href="{{ route('admin.licencas.index', array_merge(request()->except(['situacao', 'page']), ['situacao' => $situacaoAtual === $chave ? '' : $chave])) }}"
                       class="block rounded-lg border-l-4 {{ $estilos[$card['cor']]['card'] }} px-4 py-3 hover:shadow-md transition {{ $situacaoAtual === $chave ? $estilos[$card['cor']]['ring'] : '' }}">
                        <p class="text-xs font-semibold uppercase {{ $estilos[$card['cor']]['titulo'] }}">{{ $card['label'] }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $card['valor'] }}</p>
                    </a>
                @endforeach
            </div>

            {{-- Filtros --}}
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                {!! Form::open(['route' => 'admin.licencas.index', 'method' => 'GET']) !!}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <label class="block text-sm md:col-span-2">
                        <span class="text-gray-700">Empresa / CNPJ / ID:</span>
                        <input type="text" name="busca" value="{{ $filtros['busca'] ?? '' }}"
                               placeholder="Razão social, nome fantasia, CPF/CNPJ ou ID"
                               class="block w-full mt-1 text-sm form-input focus:border-purple-400 focus:outline-none focus:shadow-outline-purple">
                    </label>

                    <label class="block text-sm">
                        <span class="text-gray-700">Plano:</span>
                        {!! Form::select('plano_id', $planos, $filtros['plano_id'] ?? null, ['class' => 'block w-full mt-1 text-sm form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple']) !!}
                    </label>

                    <label class="block text-sm">
                        <span class="text-gray-700">Situação:</span>
                        {!! Form::select('situacao', $situacoes, $situacaoAtual, ['class' => 'block w-full mt-1 text-sm form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple']) !!}
                    </label>
                </div>

                <div class="flex justify-end mt-3">
                    <a href="{{ route('admin.licencas.index') }}"
                       class="inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 mr-2 bg-white text-sm font-medium text-gray-700 shadow-sm hover:text-gray-500">
                        Limpar Pesquisa
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent px-4 py-2 bg-green-600 text-sm font-medium text-white shadow-sm hover:bg-green-500">
                        Pesquisar
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        @include('components.mensagens')

        {{-- Tabela --}}
        <div class="w-full mt-5 overflow-hidden rounded-lg shadow-xs bg-white">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Razão Social</th>
                            <th class="px-4 py-3">CPF/CNPJ</th>
                            <th class="px-4 py-3">Responsável</th>
                            <th class="px-4 py-3">Plano</th>
                            <th class="px-4 py-3">Validade</th>
                            <th class="px-4 py-3">Situação</th>
                            <th class="px-4 py-3">Últ. Emissão</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @forelse ($licencas as $item)
                            @php
                                $validade = $item->validate ? \Carbon\Carbon::parse($item->validate)->startOfDay() : null;
                                $dias = $validade ? $hoje->diffInDays($validade, false) : null;

                                if (is_null($validade)) {
                                    $sit = ['Sem licença', 'gray'];
                                } elseif ($dias < 0) {
                                    $sit = ['Vencida há ' . abs($dias) . ' dia(s)', 'red'];
                                } elseif ($dias <= $diasAVencer) {
                                    $sit = $dias === 0 ? ['Vence hoje', 'orange'] : ['Vence em ' . $dias . ' dia(s)', 'orange'];
                                } else {
                                    $sit = ['Ativa', 'green'];
                                }
                            @endphp
                            <tr class="text-gray-700 text-sm {{ $sit[1] === 'red' ? 'bg-red-50' : '' }}">
                                <td class="px-4 py-3">{{ $item->id }}</td>
                                <td class="px-4 py-3 font-semibold"> {{ Str::limit($item->razao_social, 30, '...') }}</td>
                                <td class="px-4 py-3">{{ $item->cpf_cnpj }}</td>
                                <td class="px-4 py-3">{{ $item->responsavel }}</td>
                                <td class="px-4 py-3">
                                    @if ($item->plano_nome)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">{{ $item->plano_nome }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $validade ? $validade->format('d/m/Y') : '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $estilos[$sit[1]]['badge'] }}">
                                        <span class="w-2 h-2 rounded-full {{ $estilos[$sit[1]]['dot'] }} mr-1"></span>
                                        {{ $sit[0] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $item->ultima_emissao ? \Carbon\Carbon::parse($item->ultima_emissao)->format('d/m/Y') : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.licencas.edit', $item->id) }}"
                                       class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700">
                                        Editar
                                    </a>
                                    @if ($item->plano_id)
                                        <a href="{{ route('admin.licencas.licenca', $item->id) }}"
                                           class="inline-flex items-center px-3 py-2 rounded-lg bg-green-600 text-white text-xs font-semibold hover:bg-green-700">
                                            Renovar
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">
                                    Nenhuma empresa encontrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {!! $licencas->links('pagination') !!}
        </div>
    </div>
</x-area-admin-layout>
