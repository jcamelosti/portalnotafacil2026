<?php

namespace App\Services;

use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class NotaFiscalAuthorization
{
    public function autorizar($nota, $empresa_sessao): void
    {
        $usuario = Auth::user();

        // Administrador tem acesso irrestrito
        if ((int) $usuario->is_admin === 1) {
            return;
        }

        // Usuário sem empresa na sessão
        if (is_null($empresa_sessao)) {
            abort(
                Response::HTTP_FORBIDDEN,
                'Você não tem permissão para acessar esta nota.'
            );
        }

        // Verifica se o usuário possui acesso à empresa
        $empresaCompartilhada = Empresa::query()
            ->where('empresas.id', $nota->empresa_id)
            ->where(function ($query) use ($usuario) {
                $query->where('empresas.user_id', $usuario->id)
                    ->orWhereExists(function ($sub) use ($usuario) {
                        $sub->select(DB::raw(1))
                            ->from('empresas_compartilhadas')
                            ->whereColumn(
                                'empresas_compartilhadas.empresa_id',
                                'empresas.id'
                            )
                            ->where(
                                'empresas_compartilhadas.solicitante_user_id',
                                $usuario->id
                            )
                            ->where(
                                'empresas_compartilhadas.autorizado',
                                'S'
                            );
                    });
            })
            ->exists();

        $mesmaEmpresa = (int) $empresa_sessao->id
            === (int) $nota->empresa_id;

        $usuarioEhDono = (int) $nota->empresa->user_id
            === (int) $usuario->id;

        if (
            !$mesmaEmpresa &&
            !$usuarioEhDono &&
            !$empresaCompartilhada
        ) {
            abort(
                Response::HTTP_FORBIDDEN,
                'Você não tem permissão para acessar esta nota.'
            );
        }
    }
}