<x-area-cliente-layout title="Atualização Cadastral">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dados Faturamento') }}
        </h2>
    </x-slot>

    <div class="py-2 mx-auto w-full sm:px-6 lg:px-8">
        {{-- 🔔 CARD INFORMATIVO --}}
        <div class="m-6 p-5 rounded-lg bg-blue-50 border border-blue-200">
            <div class="flex items-start gap-4">
                {{-- Ícone --}}
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-100">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13 16h-1v-4h-1m1-4h.01M12 20h.01M12 4a8 8 0 100 16 8 8 0 000-16z"/>
                        </svg>
                    </div>
                </div>

                {{-- Texto --}}
                <div class="text-sm text-blue-900 leading-relaxed">

                    <p class="font-semibold mb-2">
                        Atualização obrigatória dos dados de faturamento
                    </p>

                    <p class="mb-2">
                        Para que possamos emitir corretamente as <strong>Notas Fiscais de Prestação de Serviço</strong>,
                        é necessário manter seus dados cadastrais sempre atualizados.
                    </p>

                    <ul class="list-disc pl-5 space-y-1">
                        <li>
                            Se for <strong>Pessoa Jurídica</strong>, informe corretamente o
                            <strong>CNPJ</strong>, a <strong>Razão Social</strong> e o
                            <strong>endereço completo da empresa</strong>.
                        </li>
                        <li>
                            Certifique-se de que os dados estejam iguais ao cadastro oficial.
                        </li>
                    </ul>

                    <p class="mt-3 font-medium">
                        ⚠️ A atualização correta garante o envio adequado das notas fiscais.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container grid mx-auto py-2 w-full">
        {!! Form::open(['route'=>'dados-faturamento.store', 'name'=> 'Form1']) !!}
            @include('dados-faturamento._form')
        {!! Form::close() !!}
    </div>
</div>
</x-area-cliente-layout>
