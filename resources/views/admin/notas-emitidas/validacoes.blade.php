<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Validações por Empresa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">

    <div class="card shadow-sm">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-bug-fill"></i> Validações por Empresa
            </h5>
            <span class="badge bg-light text-info">
                {{ count($resultados) }} registros
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>CPF / CNPJ</th>
                            <th>Razão Social</th>
                            <th>Validação</th>
                            <th class="text-center">Ocorrências</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resultados as $index => $r)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $r['cpf_cnpj'] }}
                                    </span>
                                </td>

                                <td>
                                    <strong>{{ $r['razao_social'] }}</strong>
                                </td>

                                <td class="text-danger">
                                    <div class="w-full whitespace-pre-wrap [overflow-wrap:anywhere]">
                                        {{ $r['mensagem'] }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Nenhum erro encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer text-muted small text-end">
            Gerado em {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
