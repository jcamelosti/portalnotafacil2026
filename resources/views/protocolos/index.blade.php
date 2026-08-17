
<x-area-empresa-layout title="Notas Emitidas">
    <div class="py-10 mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Notas Fiscais de Serviço
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Listagem de Protocolos
                </p>
            </h1>
        </div>

        <!--div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row">
            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                <a href=""
                   class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                    Consultar Situação dos Protocolos
                </a>
            </span>
        </div-->
    
        <ul class="border-collapse table-auto w-full text-sm mt-4">
            @forelse($protocolos as $key => $protocolo)
            <li class="bg-white  mt-4">
                <div class="border-b border-slate-100  p-4 pl-8 text-slate-500  {{ ($protocolo->tentativas < 2) ? 'bg-green-100': 'bg-red-100' }}">
                    <strong>Número Protocolo:</strong> {{ $protocolo->protocolo }}<br />
                    <strong>Núm. RPS:</strong> {{ $protocolo->num_nfse }}<br />
                    <strong>Valor:</strong> R$ {{$protocolo->valor_total}}<br />
                    <strong>Tomador:</strong> {{ $protocolo->tomador->razao_social }}<br />
                    <strong>Mensagem:</strong> {{$protocolo->mensagem}}<br />
                    <strong>Data/Hora:</strong> {{$protocolo->created_at->format('d/m/Y H:i:s')}}
                </div>
            </li>
            @empty
            <li>
                <div class="flex items-center bg-blue-500 text-white text-sm font-bold px-4 py-3" role="alert">
                    <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z"/></svg>
                    <p>Não há protocolos.</p>
                </div>
            </li>
            @endforelse
        </ul>
    </div>
    {{ $protocolos->render('pagination') }}
    </div>
</x-area-empresa-layout>
