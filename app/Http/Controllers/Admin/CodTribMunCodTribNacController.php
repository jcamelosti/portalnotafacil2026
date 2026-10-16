<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CorrelacaoTribMunTribNac;
use App\Models\CorrelacaoTribNacNbs;
use App\Models\Empresa;
use App\Models\EmpresaAtividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CodTribMunCodTribNacController extends Controller
{
    private $atividadeModel;
    private $consultaCorrelacaoModel;

    public function __construct(
        EmpresaAtividade $atividadeModel,
        CorrelacaoTribNacNbs $consultaCorrelacaoModel
    )
    {
        $this->atividadeModel = $atividadeModel;
        $this->consultaCorrelacaoModel = $consultaCorrelacaoModel;
    }

    public function index(Request $request, Empresa $empresa){
        $query = CorrelacaoTribMunTribNac::porEmpresa($empresa->id);

        $codTribs = $query
            ->paginate(15)
            ->withQueryString();
        
        return view('admin.empresas.correlacao-codtribmun-codtribnac.index')->with([
            'codTribs' => $codTribs,
            'empresa' => $empresa
        ]);
    }

    public function edit(Empresa $empresa, CorrelacaoTribMunTribNac $correlacaoTribMunTribNac){
        $atividades = $this->atividadeModel->atividadesByCTribMunList($empresa->id); 
    
        return view('admin.empresas.correlacao-codtribmun-codtribnac.edit')->with([
            'empresa' => $empresa,
            'correlacaoTribMunTribNac' => $correlacaoTribMunTribNac,
            'atividades' => $atividades
        ]);
    }

    public function update(Request $request, Empresa $empresa, CorrelacaoTribMunTribNac $correlacaoTribMunTribNac){
        $dados = $request->all();

        $atividade = $this->atividadeModel->where('codigo_atividade', $request->cTribMun)->first();
        
        $dados['xTribMun'] = $atividade->descricao_atividade;
        
        $correlacaoTribMunTribNac->update($dados);

        session()->flash('message', 'Registro Alterado com Sucesso.');

        return redirect()->route('admin.codtrimun-codtribnac.index', $empresa);
    }

    public function create(Empresa $empresa){
        $atividades = $this->atividadeModel
            ->atividadesByCTribMunList($empresa->id); 

        return view('admin.empresas.correlacao-codtribmun-codtribnac.create')->with([
            'empresa' => $empresa,
            'correlacaoTribMunTribNac' => new CorrelacaoTribMunTribNac(),
            'atividades' => $atividades
        ]);
    }

    public function store(Request $request, Empresa $empresa){
        /*$correlacaoTribMunTribNac = new CorrelacaoTribMunTribNac();
        $dados = $request->all();

        $atividade = $this->atividadeModel->where('codigo_atividade', $request->cTribMun)->first();
        $dados['xTribMun'] = $atividade->descricao_atividade;
        $dados['empresa_id'] = $empresa->id;
        
        $ins = $correlacaoTribMunTribNac->create($dados);

        $consultaNbs = $this->consultaCorrelacaoModel->where('c_trib_nac', $dados['cTribNac'])
            ->get(); 

        foreach($consultaNbs as $reg){
            
        }

        session()->flash('success', 'Registro adicionado com sucesso!');
        
        return redirect()->route('admin.codtrimun-codtribnac.index', [$empresa, $ins]);*/

        $existe = CorrelacaoTribMunTribNac::where('cTribMun', $request->cTribMun)
            ->where('cTribNac', $request->cTribNac)
            ->where('empresa_id', $empresa->id)
            ->first();

        if($existe){
            session()->flash('danger', 'Já existe um registro com os dados informado. Confira');
            return redirect()->route('codtrimun-codtribnac.index', $empresa);
        }

        
        try {
            $ins = DB::transaction(function () use ($request, $empresa) {
                $correlacaoTribMunTribNac = new CorrelacaoTribMunTribNac();
                $dados = $request->all();

                $atividade = $this->atividadeModel
                    ->where('codigo_atividade', $request->cTribMun)
                    ->first();

                if (!$atividade) {
                    throw new \Exception('Atividade municipal não encontrada.');
                }

                $dados['xTribMun'] = $atividade->descricao_atividade;
                $dados['empresa_id'] = $empresa->id;

                // Insere a correlação
                $ins = $correlacaoTribMunTribNac->create($dados);

                // Consulta as correlações tributárias nacionais
                $consultaNbs = $this->consultaCorrelacaoModel
                    ->where('c_trib_nac', $dados['cTribNac'])
                    ->get();

                $registroNbsEmpresa = [];
                foreach ($consultaNbs as $reg) {
                    $registroNbsEmpresa[] = [
                        'empresa_id' => $empresa->id,
                        'correlaca_trib_id' => $ins->id,
                        'codigo' => $reg->c_nbs,
                        'descricao' => $reg->x_nbs,
                    ];
                }

                foreach (array_chunk($registroNbsEmpresa, 500) as $lote) {
                    DB::table('empresa_nbs')->insert($lote);
                }

                return $ins;
            });

            session()->flash('success', 'Registro adicionado com sucesso!');

            return redirect()->route(
                'admin.codtrimun-codtribnac.index',
                [$empresa, $ins]
            );

        } catch (\Throwable $e) {
            Log::error('Erro ao cadastrar correlação tributária.', [
                'empresa_id' => $empresa->id,
                'erro' => $e->getMessage(),
            ]);

            session()->flash('danger', 'Não foi possível salvar o registro. Nenhuma alteração da transação foi mantida.');
            return back()
                ->withInput();
        }
    }
}
