<x-area-empresa-layout title="Notas Emitidas">
    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Notas Fiscais de Serviço
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Listagem de Notas Emitidas
                </p>
            </h1>
        </div>

        <div class="container grid px-6 pt-4 mx-auto">
            <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b  bg-gray-50  ">
                            <th class="px-4 py-3">Tomador</th>
                            <th class="px-4 py-3">Data Emissão</th>
                            <th class="px-4 py-3">Situação</th>
                            <!--th class="px-4 py-3">Código Verificação</th-->
                            <th class="px-4 py-3">Núm. Nota</th>
                            <th class="px-4 py-3">Núm. RPS</th>
                            <th class="px-4 py-3">Download PDF</th>
                            <th class="px-4 py-3">Download XML</th>
                            <th class="px-4 py-3">Cancelar</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y  ">
                        @foreach($notas as $key => $nota)
                            <tr class="text-gray-700 ">
                                <td class="px-4 py-3">
                                    <div class="flex items-center text-sm">
                                       {{-- <div class="relative hidden w-8 h-8 mr-3 rounded-full md:block">
                                            <div
                                                class="absolute text-center py-1 my-1 inset-0 rounded-full shadow-inner"
                                                aria-hidden="true">{{ $nota->id }}</div>
                                        </div> --}}
                                        <div>
                                            <p class="font-semibold">{{ $nota->nfse_id }}</p>
                                            <p class="text-xs text-gray-600 ">
                                                {{ $nota->tomador->razao_social }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $nota->data_emissao_br }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if($nota->situacao == 'CONCLUIDO')
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full  ">
                                     {{ $nota->situacao }}
                                </span>
                                    @else
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full  ">
                                     {{ $nota->situacao }}
                                </span>
                                    @endif
                                </td>
                                <!--td class="px-4 py-3 text-sm">
                                    { { $nota->codigo_verificacao } }
                                </td-->
                                <td class="px-4 py-3 text-sm">
                                    {{ $nota->numero_nfse }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $nota->numero_rps }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($nota->situacao == 'CONCLUIDO')
                                        <span
                                        class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full  ">
                                        <a href="{{ route('notas.pdf', [$empresa->cpf_cnpj, $nota->nfse_id]) }}">
                                            Download PDF
                                        </a>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($nota->situacao == 'CONCLUIDO')
                                        <span
                                        class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full  ">
                                        <a href="{{ route('notas.xml', [$empresa->cpf_cnpj, $nota->nfse_id]) }}">
                                            Download Xml
                                        </a>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($nota->situacao == 'CONCLUIDO')
                                        <span
                                        class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full  ">
                                        <a href="{{ route('notas.cancelar', $nota->nfse_id) }}">
                                            Cancelar
                                        </a>            
                                    @endif
                                </td>
                                {{--<td class="px-4 py-3 text-sm">
                                    R$ {{ number_format($nota->valor,2,'.', ',') }}
                                </td>--}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $notas->render('pagination') }}
            </div>
        </div>
    </div>
</x-area-empresa-layout>
