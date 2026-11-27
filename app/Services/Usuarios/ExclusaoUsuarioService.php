<?php

namespace App\Services\Usuarios;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Remove definitivamente um usuário e TODOS os registros relacionados a ele:
 * empresas próprias (e tudo que pertence a elas), faturas, certificados,
 * compartilhamentos, clientes, créditos, tokens, times e sessões.
 *
 * Empresas de terceiros compartilhadas com o usuário NÃO são removidas,
 * apenas o vínculo de compartilhamento.
 */
class ExclusaoUsuarioService
{
    /** @var array<string,bool> */
    private $tabelasExistentes = [];

    /**
     * Quantidade de registros que serão removidos, para exibir na confirmação.
     */
    public function resumo(User $user): array
    {
        $ids = $this->coletarIds($user);

        return [
            'Empresas próprias' => count($ids['empresas']),
            'Empresas compartilhadas (vínculos)' => $this->contar('empresas_compartilhadas', function ($q) use ($user, $ids) {
                $q->where('solicitante_user_id', $user->id)
                    ->orWhere('proprietario_user_id', $user->id)
                    ->orWhereIn('empresa_id', $ids['empresas']);
            }),
            'Notas emitidas' => $this->contar('notas_emitidas', fn ($q) => $q->whereIn('empresa_id', $ids['empresas'])),
            'Tomadores' => count($ids['tomadores']),
            'Faturas' => count($ids['faturas']),
            'Licenças' => $this->contar('licenses', fn ($q) => $q->whereIn('empresa_id', $ids['empresas'])),
            'Certificados digitais' => $this->contar('certificados', function ($q) use ($user, $ids) {
                $q->where('user_id', $user->id)->orWhereIn('empresa_id', $ids['empresas']);
            }),
            'Documentos fiscais recebidos' => $this->contar('documento_fiscal_recebidos', fn ($q) => $q->whereIn('empresa_id', $ids['empresas'])),
            'Clientes' => $this->contar('clientes', fn ($q) => $q->where('user_id', $user->id)),
        ];
    }

    /**
     * Executa a exclusão em uma única transação. Arquivos em disco
     * (certificados e foto de perfil) só são apagados após o commit.
     */
    public function excluir(User $user): void
    {
        $arquivosCertificado = [];
        $fotoPerfil = $user->profile_photo_path;

        DB::transaction(function () use ($user, &$arquivosCertificado) {
            $ids = $this->coletarIds($user);
            $empresas = $ids['empresas'];
            $faturas = $ids['faturas'];
            $tomadores = $ids['tomadores'];

            // ---- Faturas e cobrança --------------------------------------
            if ($faturas && $this->existe('notificacao_gateway_pagamentos')) {
                $transacoes = DB::table('faturas')->whereIn('id', $faturas)
                    ->whereNotNull('transacao_id')->pluck('transacao_id')->all();
                if ($transacoes) {
                    DB::table('notificacao_gateway_pagamentos')->whereIn('transacao_id', $transacoes)->delete();
                }
            }

            $this->apagar('servicos', function ($q) use ($faturas, $empresas, $tomadores) {
                $q->whereIn('fatura_id', $faturas)
                    ->orWhereIn('empresa_id', $empresas)
                    ->orWhereIn('empresa_cliente_id', $empresas)
                    ->orWhereIn('tomador_id', $tomadores);
            });
            $this->apagar('fatura_itens', fn ($q) => $q->whereIn('fatura_id', $faturas));
            $this->apagar('faturas', fn ($q) => $q->whereIn('id', $faturas));

            // ---- Dados das empresas próprias -----------------------------
            if ($empresas) {
                $this->apagar('notas_emitidas', function ($q) use ($empresas, $tomadores) {
                    $q->whereIn('empresa_id', $empresas)->orWhereIn('tomador_id', $tomadores);
                });
                $this->apagar('temp', function ($q) use ($empresas, $tomadores) {
                    $q->whereIn('empresa_id', $empresas)->orWhereIn('tomador_id', $tomadores);
                });

                // empresa_nbs referencia correlacao_trib_mun_trib_nac (RESTRICT): remover antes
                $correlacoes = $this->existe('correlacao_trib_mun_trib_nac')
                    ? DB::table('correlacao_trib_mun_trib_nac')->whereIn('empresa_id', $empresas)->pluck('id')->all()
                    : [];
                $this->apagar('empresa_nbs', function ($q) use ($empresas, $correlacoes) {
                    $q->whereIn('empresa_id', $empresas)->orWhereIn('correlaca_trib_id', $correlacoes);
                });
                $this->apagar('correlacao_trib_mun_trib_nac', fn ($q) => $q->whereIn('empresa_id', $empresas));

                foreach ([
                    'empresa_tributacoes',
                    'empresa_atividades',
                    'empresa_cnaes',
                    'tomadores',
                    'dfe_consultas',
                    'documento_fiscal_recebidos',
                    'internal_logs',
                    'validacoes_xml',
                    'license_logs',
                    'licenses',
                ] as $tabela) {
                    $this->apagar($tabela, fn ($q) => $q->whereIn('empresa_id', $empresas));
                }
            }

            // ---- Certificados (guarda os arquivos para remover após o commit)
            if ($this->existe('certificados')) {
                $filtroCert = function ($q) use ($user, $empresas) {
                    $q->where('user_id', $user->id)->orWhereIn('empresa_id', $empresas);
                };
                $arquivosCertificado = DB::table('certificados')->where($filtroCert)
                    ->whereNotNull('arquivo')->pluck('arquivo')->unique()->all();
                DB::table('certificados')->where($filtroCert)->delete();
            }

            // ---- Compartilhamentos e empresas ----------------------------
            $this->apagar('empresas_compartilhadas', function ($q) use ($user, $empresas) {
                $q->where('solicitante_user_id', $user->id)
                    ->orWhere('proprietario_user_id', $user->id)
                    ->orWhereIn('empresa_id', $empresas);
            });
            $this->apagar('empresas', fn ($q) => $q->whereIn('id', $empresas));

            // ---- Dados do próprio usuário --------------------------------
            $this->apagar('clientes', fn ($q) => $q->where('user_id', $user->id));
            $this->apagar('credito_historico', fn ($q) => $q->where('user_id', $user->id));
            $this->apagar('credito_users', fn ($q) => $q->where('user_id', $user->id));

            // Logs de licença de OUTRAS empresas feitos por este usuário (admin):
            // preserva a auditoria, apenas desvincula o autor.
            if ($this->existe('license_logs')) {
                DB::table('license_logs')->where('user_id', $user->id)->update(['user_id' => null]);
            }

            $this->apagar('sessions', fn ($q) => $q->where('user_id', $user->id));
            $this->apagar('personal_access_tokens', function ($q) use ($user) {
                $q->where('tokenable_type', User::class)->where('tokenable_id', $user->id);
            });
            $this->apagar('password_resets', fn ($q) => $q->where('email', $user->email));

            // Times (Jetstream)
            if ($this->existe('teams')) {
                $times = DB::table('teams')->where('user_id', $user->id)->pluck('id')->all();
                if ($times) {
                    $this->apagar('team_invitations', fn ($q) => $q->whereIn('team_id', $times));
                    $this->apagar('team_user', fn ($q) => $q->whereIn('team_id', $times));
                    DB::table('users')->whereIn('current_team_id', $times)->update(['current_team_id' => null]);
                    DB::table('teams')->whereIn('id', $times)->delete();
                }
            }
            $this->apagar('team_user', fn ($q) => $q->where('user_id', $user->id));

            DB::table('users')->where('id', $user->id)->delete();
        });

        $this->removerArquivos($arquivosCertificado, $fotoPerfil);
    }

    /**
     * IDs das empresas próprias (inclusive soft-deleted), faturas e tomadores do usuário.
     */
    private function coletarIds(User $user): array
    {
        $empresas = DB::table('empresas')->where('user_id', $user->id)->pluck('id')->all();

        $faturas = $this->existe('faturas')
            ? DB::table('faturas')
                ->where(function ($q) use ($user, $empresas) {
                    $q->where('user_id', $user->id)->orWhereIn('empresa_id', $empresas);
                })
                ->pluck('id')->all()
            : [];

        $tomadores = ($empresas && $this->existe('tomadores'))
            ? DB::table('tomadores')->whereIn('empresa_id', $empresas)->pluck('id')->all()
            : [];

        return compact('empresas', 'faturas', 'tomadores');
    }

    private function apagar(string $tabela, callable $filtro): void
    {
        if ($this->existe($tabela)) {
            DB::table($tabela)->where($filtro)->delete();
        }
    }

    private function contar(string $tabela, callable $filtro): int
    {
        return $this->existe($tabela) ? DB::table($tabela)->where($filtro)->count() : 0;
    }

    private function existe(string $tabela): bool
    {
        return $this->tabelasExistentes[$tabela] ??= Schema::hasTable($tabela);
    }

    private function removerArquivos(array $arquivosCertificado, ?string $fotoPerfil): void
    {
        foreach ($arquivosCertificado as $arquivo) {
            // O nome do arquivo é o CNPJ: só apaga se nenhum outro certificado ainda o utiliza
            if (DB::table('certificados')->where('arquivo', $arquivo)->exists()) {
                continue;
            }
            $caminho = storage_path('certificados/' . basename($arquivo));
            if (is_file($caminho) && !@unlink($caminho)) {
                Log::warning("Exclusão de usuário: não foi possível remover o certificado {$caminho}");
            }
        }

        if ($fotoPerfil) {
            try {
                Storage::disk(config('jetstream.profile_photo_disk', 'public'))->delete($fotoPerfil);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
