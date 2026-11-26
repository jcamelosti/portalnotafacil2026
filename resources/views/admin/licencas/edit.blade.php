<x-area-admin-layout title="Licença - {{ $empresa->razao_social }}">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Gestão de Licenças') }}
        </h2>
    </x-slot>

    @php
        $validadeAtual = $licenca ? \Carbon\Carbon::parse($licenca->validate)->startOfDay() : null;
        $dias = $validadeAtual ? $hoje->diffInDays($validadeAtual, false) : null;

        if (is_null($validadeAtual)) {
            $situacao = ['Sem licença cadastrada', 'bg-gray-200 text-gray-700'];
        } elseif ($dias < 0) {
            $situacao = ['Vencida há ' . abs($dias) . ' dia(s)', 'bg-red-100 text-red-700'];
        } elseif ($dias === 0) {
            $situacao = ['Vence hoje', 'bg-orange-100 text-orange-700'];
        } else {
            $situacao = ['Ativa — ' . $dias . ' dia(s) restantes', 'bg-green-100 text-green-700'];
        }

        // Base dos atalhos: validade atual (se ainda vigente) ou hoje (se vencida/sem licença)
        $dataBase = ($validadeAtual && $dias >= 0) ? $validadeAtual : $hoje;
    @endphp

    <div class="w-full px-4 py-6 sm:px-6 lg:px-8">

        <div class="bg-white shadow-xl rounded-lg py-2">
            <h1 class="px-4 py-3">
                Licença — {{ $empresa->razao_social }}
                <p class="mt-1 text-sm text-gray-500">
                    #{{ $empresa->id }} · CPF/CNPJ {{ $empresa->cpf_cnpj }}
                </p>
            </h1>

            <div class="flex flex-wrap gap-2 bg-gray-50 px-4 py-3 justify-end">
                <a href="{{ route('admin.licencas.index') }}"
                   class="px-4 py-2 border border-gray-300 bg-white text-sm rounded hover:text-gray-500">
                    Voltar para Licenças
                </a>
                <a href="{{ route('admin.empresas.edit', $empresa->id) }}"
                   class="px-4 py-2 border border-blue-700 bg-white text-sm rounded hover:text-gray-500">
                    Editar Empresa
                </a>
            </div>
        </div>

        @include('components.mensagens')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

            {{-- Formulário --}}
            <div class="lg:col-span-1 bg-white rounded-lg shadow-md px-5 py-5">
                {!! Form::open(['route' => ['admin.licencas.update', $empresa->id], 'method' => 'PUT', 'id' => 'form-licenca']) !!}

                <div class="mb-4">
                    <span class="block text-sm text-gray-700">Validade atual:</span>
                    <p class="mt-1 text-lg font-semibold text-gray-800">
                        {{ $validadeAtual ? $validadeAtual->format('d/m/Y') : '—' }}
                    </p>
                    <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold rounded-full {{ $situacao[1] }}">
                        {{ $situacao[0] }}
                    </span>
                </div>

                <label class="block text-sm mb-4">
                    <span class="text-gray-700">Plano:</span>
                    {!! Form::select('plano_id', $planos, old('plano_id', $empresa->plano_id), [
                        'id' => 'plano_id',
                        'class' => 'block w-full mt-1 text-sm form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple',
                    ]) !!}
                    @error('plano_id')
                        <span class="text-xs text-red-600"><strong>{{ $message }}</strong></span>
                    @enderror
                </label>

                <label class="block text-sm mb-2">
                    <span class="text-gray-700">Nova validade:</span>
                    <input type="date" name="validate" id="validate"
                           value="{{ old('validate', $validadeAtual ? $validadeAtual->format('Y-m-d') : '') }}"
                           class="block w-full mt-1 text-sm form-input focus:border-purple-400 focus:outline-none focus:shadow-outline-purple" required>
                    @error('validate')
                        <span class="text-xs text-red-600"><strong>{{ $message }}</strong></span>
                    @enderror
                </label>

                <div class="mb-4">
                    <span class="block text-xs text-gray-500 mb-1">
                        Atalhos (somam a partir de {{ $dataBase->format('d/m/Y') }}):
                    </span>
                    <div id="atalhos" class="flex flex-wrap gap-2"></div>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <button type="button" data-dias="0" data-base="hoje"
                                class="btn-atalho px-2 py-1 text-xs rounded border border-gray-300 bg-white hover:bg-gray-100">
                            Hoje
                        </button>
                        <button type="button" data-dias="-1" data-base="hoje"
                                class="btn-atalho px-2 py-1 text-xs rounded border border-red-500 text-red-700 bg-white hover:bg-red-50"
                                title="Define a validade para ontem: a empresa fica bloqueada imediatamente">
                            Bloquear agora
                        </button>
                    </div>
                </div>

                <label class="block text-sm mb-4">
                    <span class="text-gray-700">Motivo / Observação:</span>
                    <textarea name="observacao" rows="3" maxlength="500"
                              placeholder="Ex.: pagamento via PIX confirmado, cortesia, ajuste manual..."
                              class="block w-full mt-1 text-sm form-textarea focus:border-purple-400 focus:outline-none focus:shadow-outline-purple">{{ old('observacao') }}</textarea>
                    @error('observacao')
                        <span class="text-xs text-red-600"><strong>{{ $message }}</strong></span>
                    @enderror
                </label>

                <div class="flex justify-end">
                    <a href="{{ route('admin.licencas.index') }}"
                       class="inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 mr-2 bg-white text-sm font-medium text-gray-700 shadow-sm hover:text-gray-500">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent px-4 py-2 bg-green-600 text-sm font-medium text-white shadow-sm hover:bg-green-500">
                        Salvar
                    </button>
                </div>

                {!! Form::close() !!}
            </div>

            {{-- Histórico --}}
            <div class="lg:col-span-2 bg-white rounded-lg shadow-md overflow-hidden">
                <h3 class="px-5 py-4 text-lg font-medium text-gray-900 border-b">Histórico de alterações</h3>
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                <th class="px-4 py-3">Data</th>
                                <th class="px-4 py-3">Usuário</th>
                                <th class="px-4 py-3">Validade</th>
                                <th class="px-4 py-3">Plano</th>
                                <th class="px-4 py-3">Observação</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            @forelse ($logs as $log)
                                <tr class="text-sm text-gray-700">
                                    <td class="px-4 py-3">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3">{{ optional($log->user)->name ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        {{ $log->validate_anterior ? $log->validate_anterior->format('d/m/Y') : '—' }}
                                        &rarr;
                                        <strong>{{ $log->validate_nova ? $log->validate_nova->format('d/m/Y') : '—' }}</strong>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($log->plano_anterior_id != $log->plano_novo_id)
                                            {{ optional($log->planoAnterior)->plano_nome ?? '—' }}
                                            &rarr;
                                            <strong>{{ optional($log->planoNovo)->plano_nome ?? '—' }}</strong>
                                        @else
                                            {{ optional($log->planoNovo)->plano_nome ?? '—' }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-normal">{{ $log->observacao }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                        Nenhuma alteração registrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @section('jquery')
        <script>
            (function () {
                var variacoesPorPlano = @json($variacoesPorPlano);
                var dataBase = '{{ $dataBase->format('Y-m-d') }}';
                var hoje = '{{ $hoje->format('Y-m-d') }}';
                var classeBotao = 'btn-atalho px-2 py-1 text-xs rounded border border-blue-700 text-blue-700 bg-white hover:bg-blue-50';

                function somarDias(isoData, dias) {
                    var p = isoData.split('-');
                    var d = new Date(Date.UTC(p[0], p[1] - 1, p[2]));
                    d.setUTCDate(d.getUTCDate() + dias);
                    return d.toISOString().slice(0, 10);
                }

                function renderizarAtalhos() {
                    var planoId = $('#plano_id').val();
                    var variacoes = variacoesPorPlano[planoId] || [];
                    var $container = $('#atalhos').empty();

                    if (!variacoes.length) {
                        $container.append('<span class="text-xs text-gray-400">Plano sem variações cadastradas.</span>');
                        return;
                    }

                    variacoes.forEach(function (v) {
                        $('<button type="button">')
                            .addClass(classeBotao)
                            .attr('data-dias', v.fator_vigencia)
                            .attr('data-base', 'atual')
                            .text(v.descricao + ' (+' + v.fator_vigencia + 'd)')
                            .appendTo($container);
                    });
                }

                $(document).on('click', '.btn-atalho', function () {
                    var dias = parseInt($(this).data('dias'), 10);
                    var base = $(this).data('base') === 'hoje' ? hoje : dataBase;
                    $('#validate').val(somarDias(base, dias)).trigger('change');
                });

                $('#plano_id').on('change', renderizarAtalhos);
                renderizarAtalhos();
            })();
        </script>
    @endsection
</x-area-admin-layout>
