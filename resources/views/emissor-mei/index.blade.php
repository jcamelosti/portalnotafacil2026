<x-area-empresa-layout title="Listagem de Serviços">
<div class="py-6 w-full px-4 space-y-4">

    {{-- HEADER --}}
    <div class="bg-white shadow rounded-lg p-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-lg font-semibold">
                Notas Fiscais de Serviço (MEI)
            </h1>

            <p class="text-sm text-gray-500">
                Listagem de Serviços Prestados
            </p>
        </div>

        <a href="{{ route('servicos-mei.novo') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium text-center">
           + Novo Serviço
        </a>

    </div>

    @include('components.mensagens')

    {{-- FILTROS --}}
    <div class="bg-white shadow rounded-lg p-4">

        <form method="GET" class="flex flex-col gap-3 md:grid md:grid-cols-5">

            <div class="w-full">
                <select
                    id="empresa_select"
                    name="tomador_id"
                    placeholder="Buscar empresa por CNPJ ou Razão Social"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
                </select>
            </div>

            <input
                type="date"
                name="data_inicio"
                value="{{ request('data_inicio') }}"
                class="border rounded-lg px-3 py-2 text-sm w-full"
            >

            <input
                type="date"
                name="data_fim"
                value="{{ request('data_fim') }}"
                class="border rounded-lg px-3 py-2 text-sm w-full"
            >

            <button
                class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm w-full">
                Pesquisar
            </button>
        </form>

    </div>


    {{-- LISTAGEM EM GRADE --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

        {{-- ITEM --}}
        @forelse ($servicos as $item)
        <div class="shadow rounded-lg p-4 hover:shadow-md transition {{ !empty($item->chave_nfse) ? 'bg-green-50' : 'bg-white' }} ">

            <div class="text-xs text-gray-500">Número</div>
            <div class="font-semibold mb-2">{{ str_pad($item->id, 5, "0", STR_PAD_LEFT)}}</div>

            @if(!is_null($item->tomador_id))
            <div class="text-xs text-gray-500">CNPJ</div>
            <div class="mb-2">{{ $item->tomador->cpf_cnpj_fmt }}</div>

            <div class="text-xs text-gray-500">Razão Social/Nome</div>
            <div class="mb-2">{{ $item->tomador->razao_social }}</div>
            @else
                <div class="text-xs text-gray-500">CNPJ</div>
                <div class="mb-2">{{ $item->empresa_cliente->cpf_cnpj_fmt }}</div>

                <div class="text-xs text-gray-500">Razão Social/Nome</div>
                <div class="mb-2">{{ $item->empresa_cliente->razao_social }}</div>
            @endif
            <div class="flex justify-between text-sm mt-3">
                <div>
                    <div class="text-xs text-gray-500">Data</div>
                    <div>{{ $item->created_at->format('d/m/Y') }}</div>
                </div>

                <div class="text-right">
                    <div class="text-xs text-gray-500">Valor</div>
                    <div class="font-semibold text-green-600">
                        R$ {{ $item->valor_servico_fmt }}
                    </div>
                </div>
            </div>

            <div class="flex justify-between mt-4 text-sm">
                <a href="{{ route('servicos-mei.editar', $item->id) }}" class="text-green-900 hover:underline">
                    Editar
                </a>

                <a href="{{ route('servicos-mei.show', $item->id) }}" class="text-blue-600 hover:underline">
                    Visualizar
                </a>

                <!--a href="{{ route('servicos-mei.destroy', $item->id) }}" 
                class="text-red-600 hover:underline"
                onclick="event.preventDefault(); document.getElementById('delete-{{ $item->id }}').submit();">
                    Excluir
                </a>

                <form id="delete-{{ $item->id }}" 
                    action="{{ route('servicos-mei.destroy', $item->id) }}" 
                    method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form-->
            </div>
        </div>
        @empty
            Não há informações para Exibir
        @endforelse
    </div>

    <div class="py-0 mx-auto w-full sm:px-6 lg:px-8">
        {!! $servicos->links('pagination') !!}
    </div>
</div>
@section('jquery')
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<script>
    const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute('content');
    
    new TomSelect("#empresa_select",{
        loadThrottle: 300,
        valueField: "id",
        labelField: "razao_social",
        searchField: ["razao_social","cpf_cnpj"],

        preload: false,

        load: function(query, callback) {
            if (!query.length) return callback();
            fetch(`/c/area-cliente/consultar/tomadores/buscar?q=${encodeURIComponent(query)}`, {
                method: "GET",
                headers: {
                    "Accept": "application/json",
                    //"Content-Type": "application/json; charset=utf-8",
                    'X-CSRF-TOKEN': csrfToken
                }

            })
            .then(response => response.json())
            .then(json => {

                callback(json);

            })
            .catch(() => {
                callback();
            });

        },

        render: {

            option: function(item, escape) {

                return `
                    <div>
                        <div class="font-medium">
                            ${escape(item.razao_social)}
                        </div>

                        <div class="text-xs text-gray-500">
                            ${escape(item.cpf_cnpj)}
                        </div>
                    </div>
                `;

            },

            item: function(item, escape) {

                return `
                    <div>
                        ${escape(item.razao_social)} - ${escape(item.cpf_cnpj)}
                    </div>
                `;

            }

        }
    });
</script>
@endsection
</x-area-empresa-layout>