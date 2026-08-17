<x-area-empresa-layout title="Novo Serviço">

<div class="py-6 w-full px-4 space-y-4">

    {{-- HEADER --}}
    <div class="bg-white shadow rounded-lg p-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-lg font-semibold">
                Notas Fiscais de Serviço (MEI)
            </h1>

            <p class="text-sm text-gray-500">
                Adicionar Serviço
            </p>
        </div>

        <!--a href="{{ route('servicos-mei.index') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium text-center">
           Listar Serviços
        </a-->

    </div>

    @include('components.mensagens')

    {{-- FORM --}}
    {!! Form::open(['route'=>'servicos-mei.salvar', 'name'=> 'Form1', 'class' => 'space-y-6 w-full']) !!}
        @include('emissor-mei._form')

        {{-- BOTÃO --}}
        <div class="flex justify-end">

            <button
            type="submit"
            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg text-sm font-semibold">
                Salvar Serviço
            </button>
        </div>
    {!! Form::close() !!}
</div>

@section('jquery')
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<script>
    const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute('content');
    
    //Tomador do Serviço
    new TomSelect("#tomador_select",{
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

    //Municipio de Prestação do Serviço
    new TomSelect("#local_prestacao_servico_select",{
        loadThrottle: 300,
        valueField: "id",
        labelField: "municipio",
        searchField: ["municipio","codigo"],

        preload: false,

        load: function(query, callback) {
            if (!query.length) return callback();
            fetch(`/listar/cidades?q=${encodeURIComponent(query)}`, {
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
                            ${escape(item.municipio)}
                        </div>

                        <div class="text-xs text-gray-500">
                            ${escape(item.id)}
                        </div>
                    </div>
                `;
            },
            item: function(item, escape) {
                return `
                    <div>
                        ${escape(item.municipio)} - ${escape(item.id)}
                    </div>
                `;
            }
        }
    });

    new TomSelect("#cod_trib_nacional_id",{
        create: false,
        sortField: {field: "text"}
    });

    new TomSelect("#nbs_id",{
        create: false,
        sortField: {field: "text"}
    });
</script>

 <script src="{{ asset('js/fn_geral.js') }}"></script>
<script src="{{ asset('js/calculos_geral.js?'.time()) }}"></script>
@endsection
</x-area-empresa-layout>