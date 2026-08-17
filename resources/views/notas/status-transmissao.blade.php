<x-area-empresa-layout title="Notas Emitidas">
    <div class="container grid px-6 mx-auto w-full">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md"">
           Transmissão de NFS-e efetuada com Sucesso
        </h2>

        @include('components.mensagens')

        <div class="grid gap-6 mb-8 md:grid-cols-1">
            <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs ">
                <h4 class="mb-4 font-semibold flex items-center bg-blue-500 text-white text-sm font-bold px-4 py-3">
                    Nota Transmitida com Sucesso. Aguardando Processamento pelo WebService da Prefeitura.
                </h4>
                
                <p class="text-gray-600  font-bold">
                    Você pode acompanhar o processamento dos protocolos clicando no botão abaixo, ou acessando a opção Acompanhamento de Protocolos no menu Lateral
                </p>
    
                <span class="mt-4 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                    <a href="{{ route('protocolos.index') }}"
                        class="inline-flex justify-center w-full rounded-md border border-blue-700 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                        Acompanhamento de Protocolos
                    </a>
                </span>
            </div>
        </div>
</x-area-empresa-layout>
