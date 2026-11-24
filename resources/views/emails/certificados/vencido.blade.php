<h2>Aviso de Certificado Digital Vencido</h2>

<p>Olá,</p>

<p>
    O certificado digital da empresa <strong>{{ $data['razao_social'] }}</strong>
    @if(!empty($data['cpf_cnpj']))
        (CPF/CNPJ: {{ $data['cpf_cnpj'] }})
    @endif
    venceu em <strong>{{ $data['data_validade'] }}</strong>.
</p>

<p>
    Por segurança, o arquivo do certificado e seu cadastro foram excluídos do sistema em {{ $data['data_exclusao'] }}.
</p>

<p>
    Para continuar emitindo e consultando notas fiscais, acesse o portal e cadastre um novo certificado digital válido.
</p>

<p>Atenciosamente,<br>Equipe Nota Fácil</p>
