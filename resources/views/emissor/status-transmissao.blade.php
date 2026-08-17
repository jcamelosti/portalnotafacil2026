<x-area-empresa-layout title="Status da Transmissão da NFS-e">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Status da Transmissão da NFS-e') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Status da Transmissão da NFS-e
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                   
                </p>
            </h1>
        </div>
        @include('components.mensagens')
    </div>
    
    @if($erro)
        <div class="py-1 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <h1>Dados da NFS-e</h1>

            <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <tbody class="bg-white divide-y  ">
                            <tr class="text-gray-700 ">
                                <td class="px-4 py-3 text-sm">
                                    Status da NFS-e:
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full  ">
                                        {{ $status->situacao }}
                                    </span>
                                </td>
                            </tr>
                            <tr class="text-gray-700 ">
                                <td class="px-4 py-3 text-sm">
                                   Motivo Rejeição:
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full  ">
                                        {{ $status->mensagem }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="py-1 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <h1>Dados da NFS-e</h1>

            <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <tbody class="bg-white divide-y  ">
                            <tr class="text-gray-700 ">
                                <td class="px-4 py-3 text-sm">
                                    Status da NFS-e:
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full  ">
                                        {{ $status->situacao }}
                                    </span>
                                </td>
                            </tr>
                            <tr class="text-gray-700 ">
                                <td class="px-4 py-3 text-sm">
                                Mensagem:
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full  ">
                                        {{ !empty($status->mensagem) ? $status->mensagem : 'Nota em Processamento' }}
                                    </span>
                                </td>
                            </tr>
                            <tr class="text-gray-700 ">
                                <td class="px-4 py-3 text-sm">
                                Link para Download:
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if(!empty($status->mensagem))
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full  ">
                                        <a href="{{ route('notas.pdf', [$status->prestador,$status->id]) }}">
                                            Clique para Efetuar Download da NFS-e
                                        </a>
                                    </span>
                                    @else
                                        Nota em Processamento. Download não disponível
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    
@section('jquery')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
    let numNota = '{{ $notaId }}';
    $(function() {
       setInterval(() => {
            $.ajax({
                type      : 'GET',
                url: base_url + '/c/emissor/nota/checar/' + numNota,
                contentType: false,
                cache: false,
                processData: false,
                success : function(result){
                    if(result.situacao == 'CONCLUIDO'){
                        setInterval(() => {
                            window.location.href = base_url + '/c/emissor/nota/listagem';
                        }, 5000);
                    }
                },
                error : function(){
                    console.log('Erro Na Consulta do Status da Nota');
                }
            });
       }, 5000);
    });
</script>
@endsection
</x-area-empresa-layout>
