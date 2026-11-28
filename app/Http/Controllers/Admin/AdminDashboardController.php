<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /** Janela (em dias) para considerar uma licença "a vencer" (mesma da Gestão de Licenças). */
    const DIAS_A_VENCER = 15;

    /** Empresas vencidas há mais de N dias entram no painel de aviso de exclusão. */
    const DIAS_AVISO_EXCLUSAO = 85;

    /** Meses exibidos no gráfico de emissões. */
    const MESES_GRAFICO = 12;

    /**
     * Dashboard (BI) da área administrativa.
     */
    public function index()
    {
        $hoje = Carbon::today();

        return view('admin.dashboard_admin', [
            'hoje'              => $hoje,
            'diasAVencer'       => self::DIAS_A_VENCER,
            'diasAvisoExclusao' => self::DIAS_AVISO_EXCLUSAO,
            'licencas'          => $this->resumoLicencas($hoje),
            'notas'             => $this->resumoNotas($hoje),
            'serieMensal'       => $this->serieMensal($hoje),
            'vencidas'          => $this->empresasVencidas($hoje),
            'avisosExclusao'    => $this->avisosExclusao($hoje),
            'ultimasEmissoras'  => $this->ultimasEmissoras($hoje),
        ]);
    }

    /**
     * Última validade de licença por empresa.
     */
    private function ultimaLicenca()
    {
        return DB::table('licenses')
            ->select('empresa_id', DB::raw('MAX(validate) as validate'))
            ->groupBy('empresa_id');
    }

    /**
     * Empresas ativas (não excluídas) com a validade da última licença.
     */
    private function empresasComLicenca()
    {
        return DB::table('empresas')
            ->whereNull('empresas.deleted_at')
            ->leftJoinSub($this->ultimaLicenca(), 'lic', 'lic.empresa_id', '=', 'empresas.id');
    }

    private function resumoLicencas(Carbon $hoje)
    {
        $dataHoje = $hoje->toDateString();
        $limite = $hoje->copy()->addDays(self::DIAS_A_VENCER)->toDateString();
        $corteExclusao = $hoje->copy()->subDays(self::DIAS_AVISO_EXCLUSAO)->toDateString();

        $r = $this->empresasComLicenca()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN lic.validate IS NULL THEN 1 ELSE 0 END) as sem_licenca')
            ->selectRaw('SUM(CASE WHEN lic.validate < ? THEN 1 ELSE 0 END) as vencidas', [$dataHoje])
            ->selectRaw('SUM(CASE WHEN lic.validate < ? THEN 1 ELSE 0 END) as aviso_exclusao', [$corteExclusao])
            ->selectRaw('SUM(CASE WHEN lic.validate >= ? AND lic.validate <= ? THEN 1 ELSE 0 END) as a_vencer', [$dataHoje, $limite])
            ->selectRaw('SUM(CASE WHEN lic.validate > ? THEN 1 ELSE 0 END) as ativas', [$limite])
            ->first();

        $total = (int) $r->total;
        $dados = [
            'total'          => $total,
            'ativas'         => (int) $r->ativas,
            'a_vencer'       => (int) $r->a_vencer,
            'vencidas'       => (int) $r->vencidas,
            'sem_licenca'    => (int) $r->sem_licenca,
            'aviso_exclusao' => (int) $r->aviso_exclusao,
        ];

        // Saúde da base: % com licença em dia (ativas + a vencer)
        $dados['saude'] = $total > 0 ? round(($dados['ativas'] + $dados['a_vencer']) / $total * 100, 1) : 0;

        return $dados;
    }

    private function resumoNotas(Carbon $hoje)
    {
        $inicioMes = $hoje->copy()->startOfMonth();
        $inicioMesAnterior = $inicioMes->copy()->subMonth();
        // Mesmo período do mês anterior (dia 1 até o mesmo dia), para comparação justa
        $fimComparativo = $inicioMesAnterior->copy()->addDays($hoje->day)->min($inicioMes);

        $periodo = function ($inicio, $fim) {
            return DB::table('notas_emitidas')
                ->where('created_at', '>=', $inicio)
                ->where('created_at', '<', $fim)
                ->selectRaw('COUNT(*) as qtd')
                ->selectRaw('COALESCE(SUM(CASE WHEN COALESCE(cancelada, 0) = 0 THEN valor ELSE 0 END), 0) as valor')
                ->selectRaw('SUM(CASE WHEN COALESCE(cancelada, 0) <> 0 THEN 1 ELSE 0 END) as canceladas')
                ->selectRaw('COUNT(DISTINCT empresa_id) as empresas')
                ->first();
        };

        $amanha = $hoje->copy()->addDay();
        $atual = $periodo($inicioMes, $amanha);
        $anterior = $periodo($inicioMesAnterior, $fimComparativo);
        $hojeQtd = DB::table('notas_emitidas')->where('created_at', '>=', $hoje)->where('created_at', '<', $amanha)->count();

        $variacao = function ($atual, $anterior) {
            if ((float) $anterior == 0) {
                return (float) $atual > 0 ? null : 0; // null = "novo" (sem base de comparação)
            }
            return round(((float) $atual - (float) $anterior) / (float) $anterior * 100, 1);
        };

        $qtd = (int) $atual->qtd;
        $validas = $qtd - (int) $atual->canceladas;

        return [
            'qtd_mes'           => $qtd,
            'valor_mes'         => (float) $atual->valor,
            'canceladas_mes'    => (int) $atual->canceladas,
            'empresas_mes'      => (int) $atual->empresas,
            'ticket_medio'      => $validas > 0 ? (float) $atual->valor / $validas : 0,
            'hoje'              => $hojeQtd,
            'var_qtd'           => $variacao($qtd, $anterior->qtd),
            'var_valor'         => $variacao($atual->valor, $anterior->valor),
            'taxa_cancelamento' => $qtd > 0 ? round((int) $atual->canceladas / $qtd * 100, 1) : 0,
        ];
    }

    /**
     * Quantidade e valor de notas por mês nos últimos N meses (meses sem emissão aparecem zerados).
     */
    private function serieMensal(Carbon $hoje)
    {
        $inicio = $hoje->copy()->startOfMonth()->subMonths(self::MESES_GRAFICO - 1);

        $porMes = DB::table('notas_emitidas')
            ->where('created_at', '>=', $inicio)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as mes")
            ->selectRaw('COUNT(*) as qtd')
            ->selectRaw('COALESCE(SUM(CASE WHEN COALESCE(cancelada, 0) = 0 THEN valor ELSE 0 END), 0) as valor')
            ->groupBy('mes')
            ->get()
            ->keyBy('mes');

        $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $serie = [];
        for ($i = 0; $i < self::MESES_GRAFICO; $i++) {
            $mes = $inicio->copy()->addMonths($i);
            $chave = $mes->format('Y-m');
            $serie[] = [
                'rotulo' => $meses[$mes->month - 1] . '/' . $mes->format('y'),
                'qtd'    => isset($porMes[$chave]) ? (int) $porMes[$chave]->qtd : 0,
                'valor'  => isset($porMes[$chave]) ? (float) $porMes[$chave]->valor : 0,
                'atual'  => $i === self::MESES_GRAFICO - 1,
            ];
        }

        return $serie;
    }

    private function listaVencidas()
    {
        return $this->empresasComLicenca()
            ->leftJoin('users', 'users.id', '=', 'empresas.user_id')
            ->select(
                'empresas.id',
                'empresas.razao_social',
                'empresas.nome_fantasia',
                'empresas.cpf_cnpj',
                'users.name as responsavel',
                'users.email as responsavel_email',
                'lic.validate'
            )
            ->selectSub(function ($query) {
                $query->from('notas_emitidas')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('notas_emitidas.empresa_id', 'empresas.id');
            }, 'ultima_emissao');
    }

    /**
     * Empresas com licença vencida (até o limite do aviso de exclusão), mais recentes primeiro.
     */
    private function empresasVencidas(Carbon $hoje)
    {
        $corte = $hoje->copy()->subDays(self::DIAS_AVISO_EXCLUSAO)->toDateString();

        return $this->listaVencidas()
            ->where('lic.validate', '<', $hoje->toDateString())
            ->where('lic.validate', '>=', $corte)
            ->orderBy('lic.validate', 'DESC')
            ->limit(10)
            ->get()
            ->map(function ($e) use ($hoje) {
                $e->dias_vencida = Carbon::parse($e->validate)->diffInDays($hoje);
                return $e;
            });
    }

    /**
     * Empresas vencidas há mais de DIAS_AVISO_EXCLUSAO dias, mais antigas primeiro.
     */
    private function avisosExclusao(Carbon $hoje)
    {
        $corte = $hoje->copy()->subDays(self::DIAS_AVISO_EXCLUSAO)->toDateString();

        return $this->listaVencidas()
            ->where('lic.validate', '<', $corte)
            ->orderBy('lic.validate', 'ASC')
            ->get()
            ->map(function ($e) use ($hoje) {
                $e->dias_vencida = Carbon::parse($e->validate)->diffInDays($hoje);
                return $e;
            });
    }

    /**
     * Últimas 10 empresas distintas que emitiram notas.
     */
    private function ultimasEmissoras(Carbon $hoje)
    {
        $ultimaNota = DB::table('notas_emitidas')
            ->select('empresa_id', DB::raw('MAX(id) as nota_id'), DB::raw('COUNT(*) as total_notas'))
            ->groupBy('empresa_id');

        $inicioMes = $hoje->copy()->startOfMonth();

        return DB::table('empresas')
            ->whereNull('empresas.deleted_at')
            ->joinSub($ultimaNota, 'un', 'un.empresa_id', '=', 'empresas.id')
            ->join('notas_emitidas as n', 'n.id', '=', 'un.nota_id')
            ->leftJoinSub($this->ultimaLicenca(), 'lic', 'lic.empresa_id', '=', 'empresas.id')
            ->select(
                'empresas.id',
                'empresas.razao_social',
                'empresas.nome_fantasia',
                'empresas.cpf_cnpj',
                'n.num_nfse',
                'n.valor',
                'n.cancelada',
                'n.created_at as emitida_em',
                'un.total_notas',
                'lic.validate'
            )
            ->selectSub(function ($query) use ($inicioMes) {
                $query->from('notas_emitidas as nm')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('nm.empresa_id', 'empresas.id')
                    ->where('nm.created_at', '>=', $inicioMes);
            }, 'notas_mes')
            ->orderBy('n.created_at', 'DESC')
            ->orderBy('n.id', 'DESC')
            ->limit(10)
            ->get();
    }
}
