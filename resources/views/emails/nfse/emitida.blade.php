<h2>Nota Fiscal de Serviço Eletrônica</h2>

<p>Olá{{ $nota->tomador ? ', ' . $nota->tomador->razao_social : '' }},</p>

<p>
    Segue em anexo a NFS-e
    @if($nota->num_nfse)
        nº <strong>{{ $nota->num_nfse }}</strong>
    @endif
    emitida por <strong>{{ $nota->empresa->razao_social ?? '' }}</strong>
    @if($nota->valor)
        no valor de <strong>R$ {{ number_format($nota->valor, 2, ',', '.') }}</strong>
    @endif
    .
</p>

@if($nota->chave)
    <p>Chave de acesso: {{ $nota->chave }}</p>
@endif

<p>Atenciosamente,<br>Equipe Nota Fácil</p>
