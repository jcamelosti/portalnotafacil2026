<x-app-layout title="Dashboard">

    <!-- FULL WIDTH -->
    <div class="w-full px-4 py-6">

        <h2 class="mb-6 text-2xl font-semibold text-gray-700">
           Área do Cliente
        </h2>

        @include('components.mensagens')

        @if(auth()->user()->can_insert_credit == 1)
        <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
            <div class="flex items-center p-4 bg-white rounded-lg shadow">
                <div>
                    <p class="text-sm text-gray-600">Crédito em Conta</p>
                    <p class="text-lg font-semibold text-gray-700">
                        R$ {{ number_format(optional($credito)->credito ?? 0,2, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if($quantCompartilhamentosSolicitados > 0)
        <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
            <div class="p-4 bg-white rounded-lg shadow">
                <p class="text-sm text-blue-600">
                    Solicitação de acesso a empresas
                </p>
                <p class="text-lg font-semibold text-blue-700">
                    {{$quantCompartilhamentosSolicitados}} solicitação(ões) pendente(s)
                </p>
                <a href="{{ route('empresas.list-solicitar-acesso-empresa') }}"
                target="_blank"
                class="inline-flex items-center gap-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold px-5 py-3 rounded-lg shadow-md transition duration-300">
                
                Ver Solicitações
                </a>
            </div>
        </div>
        @endif
        
        <!-- LICENÇA -->
        @if(!$temLicencaValida)
            <div class="w-full mb-4">
                <div class="bg-red-500 text-white px-4 py-2 font-bold">ATENÇÃO</div>
                <div class="bg-red-100 px-4 py-3 text-red-700">
                    <p>Você não possui licença ativa.</p>
                </div>
            </div>
        @else
            @if($license)
                <div class="w-full mb-4">
                    <div class="bg-teal-100 px-4 py-3 shadow">
                        <p class="font-bold">Licença Ativada</p>
                        <p class="text-sm">
                            Vencimento: <strong>{{ $license->validate_pt_br }}</strong>
                        </p>
                    </div>
                </div>
            @endif
        @endif


        <div class="mb-6 w-full">
            <div class="bg-blue-600 text-white font-bold rounded-t px-4 py-3 text-lg mb-4">
                Aviso Importante
            </div>
            <p class="text-gray-700 text-center mb-4">
                A partir de 01/07/2026, haverá alterações nos valores dos planos para novos clientes.
            </p>
            <div class="mx-12 space-y-12 lg:space-y-0 lg:flex lg:gap-4 lg:items-center lg:justify-center">
                @foreach($variacaoPlanos as $chaveCor => $vPlano)
                    <div class="max-w-sm p-8 border-t-4 border-{{ $cores[$chaveCor] }}-600 rounded shadow-lg">
                        <h3 class="text-2xl text-center">{{$vPlano->descricao}}</h3>
                        <p class="text-center font-bold text-4xl">R$ {{ number_format($vPlano->valor,2, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ALERTA -->
        <div class="mb-6 w-full">
            <div class="bg-green-600 text-white font-bold rounded-t px-4 py-3 text-lg">
                Emita agora seu Certificado Digital conosco 🚀
            </div>

            <div class="bg-green-100 px-4 py-4 text-green-800">
                <p class="mb-4">
                    <b>Garanta seu certificado digital agora — emissão imediata!</b><br>
                    Atendimento rápido, seguro e 100% confiável.
                </p>

                <a href="https://wa.me/5562984018589?text=Olá,%20quero%20emitir%20um%20certificado%20digital"
                target="_blank"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-3 rounded-lg shadow-md transition duration-300">
                
                📲 Falar no WhatsApp
                </a>
            </div>
        </div>

        <!-- EMPRESAS -->
        <!--h4 class="mt-6 mb-4 text-xl font-bold text-gray-800">
            Gestão de Empresas
        </h4>

        <div class="grid gap-6 mb-8">
            <div class="p-6 bg-white rounded-xl shadow">

                <h4 class="text-lg font-semibold mb-2">
                    Adicione novas Empresas
                </h4>

                <p class="text-gray-600 mb-4">
                    Cadastre empresas e gerencie licenças de forma individual.
                </p>

                <a href="{{ route('empresas.adicionar') }}"
                   class="inline-block px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Adicionar Empresa
                </a>

            </div>
        </div-->

        <!-- SUPORTE -->
        <h4 class="mb-4 text-lg font-semibold text-gray-600">
            Precisando de ajuda? Fale conosco!
        </h4>

        <div class="grid gap-6">
            <div class="p-4 bg-white rounded-lg shadow">
                <h4 class="font-semibold text-green-600 mb-2">
                   Suporte Técnico
                </h4>
                <p class="text-gray-600">
                   Atendimento: 08h às 17h<br>
                   Whatsapp: (62) 98401-8589
                </p>
            </div>
        </div>
    </div>

</x-app-layout>