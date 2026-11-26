<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateLicencaRequest;
use App\Models\Empresa;
use App\Models\License;
use App\Models\LicenseLog;
use App\Models\Plano;
use App\Models\PlanoVariacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LicencaController extends Controller
{
    /** Janela (em dias) para considerar uma licença "a vencer". */
    const DIAS_A_VENCER = 15;

    private $licencaModel;
    private $empresaModel;
    private $variacaoPlanoModel;

    public function __construct(
        License $licencaModel,
        Empresa $empresaModel,
        PlanoVariacao $variacaoPlanoModel
    )
    {
        $this->licencaModel = $licencaModel;
        $this->empresaModel = $empresaModel;
        $this->variacaoPlanoModel = $variacaoPlanoModel;
    }

    /**
     * Gestão de Licenças: listagem com contadores e filtros.
     */
    public function index(Request $request)
    {
        $filtros = $request->only(['busca', 'plano_id', 'situacao']);
        $hoje = Carbon::today()->toDateString();
        $limite = Carbon::today()->addDays(self::DIAS_A_VENCER)->toDateString();

        // Contadores respeitam busca/plano, mas não a situação (servem de filtro rápido)
        $contadores = $this->queryBase($filtros)
            ->selectRaw('SUM(CASE WHEN lic.validate IS NULL THEN 1 ELSE 0 END) as sem_licenca')
            ->selectRaw('SUM(CASE WHEN lic.validate < ? THEN 1 ELSE 0 END) as vencidas', [$hoje])
            ->selectRaw('SUM(CASE WHEN lic.validate >= ? AND lic.validate <= ? THEN 1 ELSE 0 END) as a_vencer', [$hoje, $limite])
            ->selectRaw('SUM(CASE WHEN lic.validate > ? THEN 1 ELSE 0 END) as ativas', [$limite])
            ->first();

        $licencas = $this->queryBase($filtros)
            ->select(
                'empresas.id',
                'empresas.razao_social',
                'empresas.cpf_cnpj',
                'empresas.plano_id',
                'planos.plano_nome',
                'users.name as responsavel',
                'lic.validate'
            )
            ->selectSub(function ($query) {
                $query->from('notas_emitidas')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('notas_emitidas.empresa_id', 'empresas.id');
            }, 'ultima_emissao')
            ->leftJoin('planos', 'planos.id', '=', 'empresas.plano_id')
            ->leftJoin('users', 'users.id', '=', 'empresas.user_id')
            ->when(isset($filtros['situacao']) && $filtros['situacao'] !== '', function ($query) use ($filtros, $hoje, $limite) {
                switch ($filtros['situacao']) {
                    case 'sem_licenca':
                        $query->whereNull('lic.validate');
                        break;
                    case 'vencidas':
                        $query->where('lic.validate', '<', $hoje);
                        break;
                    case 'a_vencer':
                        $query->whereBetween('lic.validate', [$hoje, $limite]);
                        break;
                    case 'ativas':
                        $query->where('lic.validate', '>', $limite);
                        break;
                }
            })
            ->orderByRaw('lic.validate IS NULL DESC')
            ->orderBy('lic.validate', 'ASC')
            ->orderBy('empresas.razao_social', 'ASC')
            ->paginate(20)
            ->withQueryString();

        $planos = (new Plano())->list();

        return view('admin.licencas.index', [
            'licencas' => $licencas,
            'contadores' => $contadores,
            'planos' => $planos,
            'filtros' => $filtros,
            'hoje' => Carbon::today(),
            'diasAVencer' => self::DIAS_A_VENCER,
        ]);
    }

    /**
     * Formulário de edição da licença de uma empresa.
     */
    public function edit(Empresa $empresa)
    {
        $licenca = $this->licencaAtual($empresa->id);
        $planos = (new Plano())->list();

        // Variações de todos os planos, para montar os atalhos "+N dias" no front
        $variacoesPorPlano = $this->variacaoPlanoModel
            ->orderBy('fator_vigencia')
            ->get(['plano_id', 'descricao', 'fator_vigencia'])
            ->groupBy('plano_id');

        $logs = LicenseLog::with(['user', 'planoAnterior', 'planoNovo'])
            ->where('empresa_id', $empresa->id)
            ->orderBy('id', 'DESC')
            ->limit(50)
            ->get();

        return view('admin.licencas.edit', [
            'empresa' => $empresa,
            'licenca' => $licenca,
            'planos' => $planos,
            'variacoesPorPlano' => $variacoesPorPlano,
            'logs' => $logs,
            'hoje' => Carbon::today(),
        ]);
    }

    /**
     * Atualiza validade/plano da licença e registra histórico.
     */
    public function update(UpdateLicencaRequest $request, Empresa $empresa)
    {
        $dados = $request->validated();

        $alterou = DB::transaction(function () use ($dados, $empresa) {
            $licenca = $this->licencaAtual($empresa->id);
            $validateAnterior = $licenca ? $licenca->validate : null;
            $planoAnterior = $empresa->plano_id;

            if ($validateAnterior == $dados['validate'] && $planoAnterior == $dados['plano_id']) {
                return false;
            }

            if (is_null($licenca)) {
                $licenca = $this->licencaModel->create([
                    'empresa_id' => $empresa->id,
                    'validate' => $dados['validate'],
                ]);
            } else {
                $licenca->validate = $dados['validate'];
                $licenca->save();
            }

            if ($planoAnterior != $dados['plano_id']) {
                $empresa->plano_id = $dados['plano_id'];
                $empresa->save();
            }

            $this->registrarLog(
                $licenca,
                $validateAnterior,
                $dados['validate'],
                $planoAnterior,
                $dados['plano_id'],
                $dados['observacao'] ?? null
            );

            return true;
        });

        if (!$alterou) {
            session()->flash('info', 'Nenhuma alteração foi realizada.');
            return redirect()->route('admin.licencas.edit', $empresa->id);
        }

        session()->flash('success', 'Licença de <b>' . e($empresa->razao_social) . '</b> atualizada com sucesso.');
        return redirect()->route('admin.licencas.edit', $empresa->id);
    }

    public function licenca($id){
        $empresa = $this->empresaModel->find($id);
        $plano = $empresa->plano()->first();
        $planoVariacoes = $this->variacaoPlanoModel->where('plano_id', $empresa->plano_id)->get();
        $cores = [
            0 => 'indigo',
            1 => 'green',
            2 => 'orange',
            3 => 'blue',
            4 => 'red',
            5 => 'yellow'
        ];

        shuffle($cores);

        session()->put('empresa_id', $id);

        return view('admin.licencas.atual')->with(
            [
                'empresa' => $empresa,
                'plano' => $plano,
                'planoVariacoes' => $planoVariacoes,
                'cores' => $cores
            ]
        );
    }

    public function renovar($varicaoPlanoId){
        $varicaoPlanoId = base64_decode($varicaoPlanoId);
        $empresaId = session()->get('empresa_id');
        $variacaoPlano = $this->variacaoPlanoModel->find($varicaoPlanoId);

        if (is_null($empresaId) || is_null($variacaoPlano)) {
            session()->flash('danger', 'Não foi possível identificar a empresa ou o plano para renovação.');
            return redirect()->route('admin.licencas.index');
        }

        $l = $this->licencaAtual($empresaId);

        if (is_null($l)) {
            $l = $this->licencaModel->create([
                'empresa_id' => $empresaId,
                'validate' => Carbon::now()->subDay(1)->format('Y-m-d')
            ]);
        }

        $validateAnterior = $l->validate;

        // Se vencida, renova a partir de hoje; senão, soma à validade atual
        $dataBase = Carbon::parse($l->validate)->isPast()
            ? Carbon::today()
            : Carbon::parse($l->validate);

        $l->validate = $dataBase->addDays($variacaoPlano->fator_vigencia)->format('Y-m-d');
        $l->save();

        $planoId = optional($this->empresaModel->find($empresaId))->plano_id;
        $this->registrarLog(
            $l,
            $validateAnterior,
            $l->validate,
            $planoId,
            $planoId,
            'Renovação: ' . $variacaoPlano->descricao
        );

        return redirect()->route('admin.empresas.index', ['empresa_id' => $empresaId]);
    }

    public function vencimentosLicencas(){
        $licVenc = $this->licencaModel
            ->select(
                'empresas.id',
                'empresas.razao_social',
                'empresas.cpf_cnpj',
                'licenses.validate as validate_licenca',
                'planos.id as plano_id',
                'planos.plano_nome',
                'users.name'
            )
            ->selectSub(function ($query) {
                $query->from('notas_emitidas')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('notas_emitidas.empresa_id', 'empresas.id');
            }, 'ultima_emissao')
            ->join('empresas', 'empresas.id', '=', 'licenses.empresa_id')
            ->join('planos', 'planos.id', '=', 'empresas.plano_id')
            ->join('users', 'users.id', '=', 'empresas.user_id')
            ->whereDate('validate', '<=', Carbon::now())
            ->where('empresas.id', '<>', 1)
            ->orderBy('validate', 'ASC')
            ->orderBy('ultima_emissao', 'ASC')
            ->get();

        $licAVenc = $this->licencaModel
            ->select(
                'empresas.id',
                'empresas.razao_social',
                'empresas.cpf_cnpj',
                'licenses.validate as validate_licenca',
                'planos.id as plano_id',
                'planos.plano_nome',
                'users.name'
            )
            ->selectSub(function ($query) {
                $query->from('notas_emitidas')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('notas_emitidas.empresa_id', 'empresas.id');
            }, 'ultima_emissao')
            ->join('empresas','empresas.id','=','licenses.empresa_id')
            ->join('planos', 'planos.id', 'empresas.plano_id')
            ->join('users', 'users.id', 'empresas.user_id')
            ->whereDate('validate', '>', Carbon::now())
            ->whereDate('validate', '<=', Carbon::now()->addDays(self::DIAS_A_VENCER))
            ->where('empresas.id', '<>', 1)
            ->orderBy('validate', 'ASC')
            ->orderBy('ultima_emissao', 'ASC')
            ->get();

        return view('admin.licencas.show')->with(['licVenc' => $licVenc, 'licAVenc' => $licAVenc]);
    }

    /**
     * Empresas + maior validade de licença de cada uma (alias "lic"),
     * com filtros de busca e plano aplicados.
     */
    private function queryBase(array $filtros)
    {
        $ultimaLicenca = DB::table('licenses')
            ->select('empresa_id', DB::raw('MAX(validate) as validate'))
            ->groupBy('empresa_id');

        return $this->empresaModel->newQuery()
            ->leftJoinSub($ultimaLicenca, 'lic', 'lic.empresa_id', '=', 'empresas.id')
            ->when(!empty($filtros['busca']), function ($query) use ($filtros) {
                $busca = trim($filtros['busca']);
                $numeros = preg_replace('/\D/', '', $busca);

                $query->where(function ($q) use ($busca, $numeros) {
                    $q->where('empresas.razao_social', 'like', "%{$busca}%")
                        ->orWhere('empresas.nome_fantasia', 'like', "%{$busca}%")
                        ->orWhere('empresas.cpf_cnpj', 'like', "%{$busca}%");

                    if ($numeros !== '') {
                        $q->orWhere('empresas.cpf_cnpj', 'like', "%{$numeros}%");
                        if (ctype_digit($busca)) {
                            $q->orWhere('empresas.id', (int) $busca);
                        }
                    }
                });
            })
            ->when(!empty($filtros['plano_id']), function ($query) use ($filtros) {
                $query->where('empresas.plano_id', $filtros['plano_id']);
            });
    }

    /**
     * Licença que determina a situação da empresa (maior validade).
     */
    private function licencaAtual($empresaId)
    {
        return $this->licencaModel
            ->where('empresa_id', $empresaId)
            ->orderBy('validate', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();
    }

    private function registrarLog(License $licenca, $validateAnterior, $validateNova, $planoAnterior, $planoNovo, $observacao = null)
    {
        LicenseLog::create([
            'license_id' => $licenca->id,
            'empresa_id' => $licenca->empresa_id,
            'user_id' => auth()->id(),
            'validate_anterior' => $validateAnterior,
            'validate_nova' => $validateNova,
            'plano_anterior_id' => $planoAnterior,
            'plano_novo_id' => $planoNovo,
            'observacao' => $observacao,
        ]);
    }
}
