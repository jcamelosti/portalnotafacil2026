<?php

namespace App\Http\Middleware;

use App\Models\Cliente;
use App\Models\Empresa;
use Closure;
use Illuminate\Http\Request;

class EnsureDadosFaturamento
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // 🚀 IGNORAR ROTAS DE CADASTRO
        if ($request->routeIs('dados-faturamento.create') ||
            $request->routeIs('dados-faturamento.store') ||
            $request->routeIs('dados-faturamento.edit') ||
            $request->routeIs('dados-faturamento.update')
        ) {
            return $next($request);
        }

        // usuário não logado
        if (!$user) {
            return redirect()->route('login');
        }

        /*Cliente::firstOrCreate(
            ['user_id' => $user->id], // condição
            [] // dados adicionais se precisar
        );*/

        // pega dados do cliente/tomador
        $cliente = $user->cliente()->first();

        $qtdEmpresasUser = Empresa::where('user_id', $user->id)->count();

        if((int)$user->can_insert_credit == 1 && $qtdEmpresasUser > 1){
            // campos obrigatórios
            $camposObrigatorios = [
                'razao_social',
                'cpf_cnpj',
                'cep',
                //'numero',
                'endereco',
                'complemento',
                'bairro',
                'cidade_id',
                'telefone1',
            ];

            // se não existir cliente
            if (!$cliente) {
                return redirect()
                    ->route('dados-faturamento.create')
                    ->with('warning', 'Complete seus dados de faturamento.');
            }

            // verifica campos vazios
            foreach ($camposObrigatorios as $campo) {
                if (empty($cliente->$campo)) {
                    return redirect()
                        ->route('dados-faturamento.edit')
                        ->with('warning', 'Complete seus dados de faturamento.');
                }
            }
        }

        return $next($request);
    }
}
