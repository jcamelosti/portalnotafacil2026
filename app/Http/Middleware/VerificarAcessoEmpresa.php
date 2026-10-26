<?php

namespace App\Http\Middleware;

use App\Models\Empresa;
use Closure;
use Illuminate\Http\Request;

class VerificarAcessoEmpresa
{
    public function handle(Request $request, Closure $next)
    {
        $usuarioId = auth()->id();

        // Obtém o ID da empresa informado na rota
        if(!request()->session()->has('empresa')){
            return $next($request);
        }

        if (
            $request->routeIs('servicos-tomados.selecionar-empresa') || $request->routeIs('empresas.selecionar-empresa')
        ) {
            return $next($request);
        }

        $empresaId = request()->session()->get('empresa')->id;
        // Se a rota utiliza Route Model Binding
        if ($empresaId instanceof Empresa) {
            $empresaId = $empresaId->id;
        }

        if (!$usuarioId || !$empresaId) {
            abort(403, 'Acesso não autorizado.');
        }

        $temAcesso = Empresa::query()
            ->where('empresas.id', $empresaId)
            ->where(function ($query) use ($usuarioId) {

                // Proprietário da empresa
                $query->where('empresas.user_id', $usuarioId)

                    // Usuário com compartilhamento autorizado
                    ->orWhereExists(function ($sub) use ($usuarioId) {
                        $sub->selectRaw('1')
                            ->from('empresas_compartilhadas as ec')
                            ->whereColumn(
                                'ec.empresa_id',
                                'empresas.id'
                            )
                            ->where(
                                'ec.solicitante_user_id',
                                $usuarioId
                            )
                            ->where('ec.autorizado', 'S');
                    });
            })
            ->exists();

        if (!$temAcesso) {
            abort(403, 'Você não possui permissão para acessar esta empresa.' . __CLASS__);
        }

        return $next($request);
    }
}