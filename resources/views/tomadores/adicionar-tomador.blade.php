<x-area-empresa-layout title="Tomadores">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Tomadores') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Tomadores
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Adicionar
                </p>
            </h1>

            <div class="px-4 py-3 mb-8 bg-white ">
                {!! Form::open(['route'=>'tomadores.adicionar', 'method'=>'POST']) !!}
                <label class="block text-sm">
                    <span class="text-gray-700 ">Informe o CNPJ do Tomador a ser Cadastrado:</span>
                    {!! Form::text('cpf_cnpj', '', ['id' => 'cpf_cnpj','class'=>'block w-full mt-1 text-sm    form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple :shadow-outline-gray']) !!}
                    @if ($errors->has('tomador_id'))
                        <span class="text-xs text-red-600 ">
                            <strong>{{ $errors->first('cpf_cnpj') }}</strong>
                        </span>
                    @endif
                </label>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                        <button type="submit" type="button"
                                class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                        Pesquisar
                      </button>
                    </span>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        @include('components.mensagens')

        <div class="container-list">
            
        </div>
    </div>

    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        
    </div>

    
@section('jquery')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-masker/1.2.0/vanilla-masker.min.js"
        integrity="sha512-RbMQw6xKGymv6bRMO4z5OxHBzzem7BPEQX7nTJC9G08A70gXdUka76Rvgey83MsSXrIEJddog0vxUKN6iTce2Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.es.gov.br/scripts/jquery/jquery-maskedinput/1.4.1/jquery.maskedinput-1.4.1.min.js"></script>
<script type="text/javascript">
    function triggerEvent(el, type) {
        if ('createEvent' in document) {
            var e = document.createEvent('HTMLEvents');
            e.initEvent(type, false, true);
            el.dispatchEvent(e);
        }
    }

    function inputHandler(masks, max, event) {
        var c = event.target;
        var v = c.value.replace(/\D/g, '');
        var m = c.value.length > max ? 1 : 0;
        VMasker(c).unMask();
        VMasker(c).maskPattern(masks[m]);
        c.value = VMasker.toPattern(v, masks[m]);
    }

    //CPF/CNPJ
    var docMask = ['999.999.999-99', '99.999.999/9999-99'];
    var doc1 = document.querySelector('#cpf_cnpj');
    var doc2 = document.querySelector('#cpf_cnpj');
    if(doc1 != null) {
        if(doc1.value.length <= 14){
            VMasker(doc1).maskPattern(docMask[0]);
            doc1.addEventListener('input', inputHandler.bind(undefined, docMask, 14), false);
            triggerEvent(doc1, 'keyup');
        }
    }

    if(doc2 != null) {
        VMasker(doc2).maskPattern(docMask[1]);
        if(doc2.value.length == 18) {
            doc2.addEventListener('input', inputHandler.bind(undefined, docMask, 18), false);
            triggerEvent(doc1, 'keyup');
        }
    }
</script>

@endsection

</x-area-empresa-layout>
