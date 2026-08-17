<x-area-empresa-layout title="Notas Emitidas">
    <div class="py-10 mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Notas Fiscais de Serviço
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Protocolo de Emissão
                </p>
            </h1>
        </div>
    </div>

    <div class="container grid px-6 pt-4 mx-auto">
        <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                @include('components.mensagens')
                <h1>Houve falha na Transmissão da Nota</h1>
                <div role="alert">
                    <div class="bg-red-500 text-white font-bold rounded-t px-4 py-2">
                      Detalhes do Erro
                    </div>
                    <div class="border border-t-0 border-red-400 rounded-b bg-red-100 px-4 py-3 text-red-700">
                        @if(isset($erro['tcCodigo']))
                            <p>Código do Erro: {{$erro['tcCodigo']}}</p><br />
                            <p>{{$erro['tcMensagem']}}</p>
                        @else
                            <p>Código do Erro: {{$erro['Codigo']}}</p><br />
                            <p>{{$erro['Mensagem']}}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-area-empresa-layout>
