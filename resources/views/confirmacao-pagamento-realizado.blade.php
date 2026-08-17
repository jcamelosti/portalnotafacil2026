<x-app-layout title="Pagamento Realizado">
    <div class="container grid px-6 mx-auto w-full min-h-screen">

        <div class="flex items-center justify-center w-full">
            <div class="bg-white shadow-lg rounded-lg p-10 text-center max-w-2xl w-full">

                <div class="flex justify-center mb-6">
                    <div class="p-4 bg-green-100 rounded-full">
                        <svg class="w-12 h-12 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-7.071 7.071a1 1 0 01-1.414 0L3.293 8.85a1 1 0 011.414-1.414l3.121 3.121 6.364-6.364a1 1 0 011.415 0z"
                                clip-rule="evenodd">
                            </path>
                        </svg>
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-gray-700 mb-4">
                    Pagamento realizado com sucesso!
                </h1>

                <p class="text-gray-600 text-lg mb-6">
                    Recebemos a confirmação do seu pagamento.
                </p>

                <p class="text-gray-600 mb-6">
                    Assim que o sistema de pagamento nos enviar a notificação oficial da transação,
                    <strong>seu crédito ou sua licença será liberado automaticamente em sua conta.</strong>
                </p>

                <p class="text-sm text-gray-500">
                    Esse processo normalmente ocorre em poucos instantes, mas pode levar alguns minutos dependendo da operadora de pagamento.
                </p>

                <div class="mt-8">
                    <a href="{{ route('dashboard') }}"
                       class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded shadow">
                        Voltar para o painel
                    </a>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>