<h1>Avisa de Vencimento de Licenças</h1>

<table border="1">
    <thead>
        <tr>
            <th>Empresa</th>
            <th>CPF/CNPJ</th>
            <th>Vencimento</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data['registros'] as $item)
            <tr>
                <td>{{ $item->razao_social}}</td>
                <td>{{ $item->cpf_cnpj}}</td>
                <td>{{ date('d/m/Y', strtotime($item->validate)) }}</td>
            </tr>
        @empty
            
            <tr>
                <td colspan="3">Não há informações para Exibir</td>
            </tr>
        @endforelse
    </tbody>
</table>

