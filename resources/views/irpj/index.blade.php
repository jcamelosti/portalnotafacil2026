<x-area-empresa-layout title="Relatório IRPJ">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Relatório IRPJ') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto w-full sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Relatório de Notas Emitidas
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Geração de Relatório
                </p>
            </h1>

            <div class="px-4 py-3 mb-8 bg-white ">
                <p>Manual Exportação Notas IssNet <a class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300  :bg-blue-700 :ring-blue-800" href="https://portalnotafacil.com.br/manual_relatorio_imposto_renda.pdf">Download</a></p><br />

                {!! Form::open(['route'=>'irpj.upload', 'method'=>'post', 'enctype'=> 'multipart/form-data']) !!}
                <label class="block text-sm">
                    <span class="text-gray-700 ">Arquivo Zip - Exportado do ISSNet:</span>           
                </label>
                {!! Form::file('arquivo', ['accept'=> '.zip','class'=>'block w-full mt-1 text-sm  
                focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                :shadow-outline-gray form-input']) !!}         
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                        <button type="submit" type="button"
                                class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                        Gerar Relatório de Notas
                      </button>
                    </span>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        @include('components.mensagens')
    </div>
</x-area-empresa-layout>
