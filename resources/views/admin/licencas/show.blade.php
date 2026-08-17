<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Vencimento de Licenças - Empresas</title>
  </head>
  <body>
    <div class="container mt-4">
        <h1>Licenças Vencidas</h1>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Seq.</th>
                        <th>#</th>
                        <th>Razão Social</th>
                        <th>CPF/CNPJ</th>
                        <th>Usuário Responsável</th>
                        <th>Plano</th>
                        <th>Validade Licença</th>
                        <th>Dt. Últ. Emissão</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($licVenc as $idx => $empresa)
                        @php
                            $vencida = \Carbon\Carbon::parse($empresa->validate_licenca)->isPast();
                        @endphp

                        <tr class="{{ $vencida ? 'table-danger' : '' }}">
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $empresa->id }}</td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $empresa->razao_social }}
                                </div>
                            </td>

                            <td>
                                {{ $empresa->cpf_cnpj }}
                            </td>

                            <td>
                                {{ $empresa->name }}
                            </td>

                            <td>
                                <span class="badge bg-primary">
                                    {{ $empresa->plano_nome }}
                                </span>
                            </td>

                            <td>
                                <span class="{{ $vencida ? 'text-danger fw-semibold' : 'text-muted' }}">
                                    {{ \Carbon\Carbon::parse($empresa->validate_licenca)->format('d/m/Y') }}
                                </span>
                            </td>
                           
                            <td>
                                <span class="{{ $vencida ? 'text-danger fw-semibold' : 'text-muted' }}">
                                    @if(!is_null($empresa->ultima_emissao))
                                        {{ \Carbon\Carbon::parse($empresa->ultima_emissao)->format('d/m/Y') }}
                                    @endif
                                </span> 
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Nenhum registro encontrado
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h1>Licenças a Vencer</h1>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Seq.</th>
                        <th>#</th>
                        <th>Razão Social</th>
                        <th>CPF/CNPJ</th>
                        <th>Usuário Responsável</th>
                        <th>Plano</th>
                        <th>Validade Licença</th>
                        <th>Dt. Últ. Emissão</th>
                        <!--th class="text-end">Ações</th-->
                    </tr>
                </thead>

                <tbody>
                    @forelse($licAVenc as $idx => $empresa)
                        @php
                            $vencida = \Carbon\Carbon::parse($empresa->validate_licenca)->isPast();
                        @endphp

                        <tr class="{{ $vencida ? 'table-danger' : '' }}">
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $empresa->id }}</td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $empresa->razao_social }}
                                </div>
                            </td>

                            <td>
                                {{ $empresa->cpf_cnpj }}
                            </td>

                            <td>
                                {{ $empresa->name }}
                            </td>

                            <td>
                                <span class="badge bg-primary">
                                    {{ $empresa->plano_nome }}
                                </span>
                            </td>

                            <td>
                                <span class="{{ $vencida ? 'text-danger fw-semibold' : 'text-muted' }}">
                                    {{ \Carbon\Carbon::parse($empresa->validate_licenca)->format('d/m/Y') }}
                                </span>
                            </td>

                            <td>
                                <span class="{{ $vencida ? 'text-danger fw-semibold' : 'text-muted' }}">
                                    @if(!is_null($empresa->ultima_emissao))
                                        {{ \Carbon\Carbon::parse($empresa->ultima_emissao)->format('d/m/Y') }}
                                    @endif
                                </span> 
                            </td>

                            <!--td class="text-end">
                                <a href="{{ route('empresas.show', $empresa->id) }}" 
                                class="btn btn-outline-primary btn-sm">
                                    Ver
                                </a>

                                <a href="{{ route('empresas.edit', $empresa->id) }}" 
                                class="btn btn-outline-secondary btn-sm">
                                    Editar
                                </a>
                            </td-->
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Nenhum registro encontrado
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>