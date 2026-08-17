@if(isset($empresa->razao_social) &&  !is_null($empresa->razao_social))
    <div class="bg-gray-200 p-4 rounded-lg text-center">
        <span class="text-2xl font-semibold text-blue-700">Empresa Emitente: {{ $empresa->razao_social }}</span>
    </div>
@elseif(isset($empresa->nome_fantasia) && !is_null($empresa->nome_fantasia))
    <div class="bg-gray-200 p-4 rounded-lg text-center">
        <span class="text-2xl font-semibold text-blue-700">Empresa Emitente: {{ $empresa->nome_fantasia }}</span>
    </div>
@else

@endif