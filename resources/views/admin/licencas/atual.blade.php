<x-area-admin-layout title="Renovação de Licença de Uso">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Renovação de Licença de Uso') }}
        </h2>
    </x-slot>

    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-1">
            <h1 class="px-4 py-3">
                Renovação de Licença de Uso
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Escolha um Plano
                </p>
            </h1>
        </div>
    </div>

    @include('components.mensagens')

    <div class="py-1 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="mt-5 md:mt-0 md:col-span-12">
            <div class="px-4 py-5 mb-8 bg-white rounded-lg shadow-md ">
                <h3 class="text-lg font-medium text-gray-900 ">
                    {{ $empresa->razao_social }}
                </h3>
            </div>
        </div>

        <div class="text-center my-10">
            <h1 class="font-bold text-3xl mb-2">{{$plano->plano_nome}}</h1>
        </div>

        <div class="mx-12 space-y-12 lg:space-y-0 lg:flex lg:gap-4 lg:items-center lg:justify-center">
            @foreach($planoVariacoes as $chaveCor => $vPlano)
                <div class="max-w-sm p-8 border-t-4 border-{{ $cores[$chaveCor] }}-600 rounded shadow-lg">
                    <h3 class="text-2xl text-center">{{$vPlano->descricao}}</h3>
                    <p class="text-center font-bold text-4xl">R$ {{ number_format($vPlano->valor,2, ',', '.') }}</p>
                    <div>
                        {{--<ul class="space-y-4 list-disc">
                            @foreach(explode(';', $plano->plano_detalhes) as $det)
                                <li>
                                    {{ $det }}
                                </li>
                            @endforeach
                        </ul>--}}
                        <div class="flex items-center justify-center mt-4">
                            <a href="{{ route('admin.licencas.renovar', base64_encode($vPlano->id)) }}" class="text-center w-full px-2 py-2 text-2xl text-{{ $cores[$chaveCor] }}-200 bg-{{ $cores[$chaveCor] }}-600 rounded">Renovar {{$vPlano->descricao}}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @section('jquery')
    @endsection
</x-area-admin-layout>