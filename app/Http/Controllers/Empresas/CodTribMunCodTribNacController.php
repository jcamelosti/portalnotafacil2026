<?php

namespace App\Http\Controllers\Empresas;

use App\Http\Controllers\Controller;
use App\Models\CorrelacaoTribMunTribNac;
use App\Models\Empresa;
use App\Models\EmpresaAtividade;
use Illuminate\Http\Request;

class CodTribMunCodTribNacController extends Controller
{
    private $atividadeModel;

    public function __construct(
        EmpresaAtividade $atividadeModel
    )
    {
        $this->atividadeModel = $atividadeModel;
    }

    public function index(Request $request, Empresa $empresa){
        $query = CorrelacaoTribMunTribNac::porEmpresa($empresa->id);

        $codTribs = $query
            ->paginate(15)
            ->withQueryString();
        
        return view('empresas.correlacao-codtribmun-codtribnac.index')->with([
            'codTribs' => $codTribs,
            'empresa' => $empresa
        ]);
    }

    public function edit(Empresa $empresa, CorrelacaoTribMunTribNac $correlacaoTribMunTribNac){
        $atividades = $this->atividadeModel->atividadesByCTribMunList($empresa->id); 
    
        return view('empresas.correlacao-codtribmun-codtribnac.edit')->with([
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

        return redirect()->route('codtrimun-codtribnac.index', $empresa);
    }

    public function create(Empresa $empresa){
        $atividades = $this->atividadeModel
            ->atividadesByCTribMunList($empresa->id); 

        return view('empresas.correlacao-codtribmun-codtribnac.create')->with([
            'empresa' => $empresa,
            'correlacaoTribMunTribNac' => new CorrelacaoTribMunTribNac(),
            'atividades' => $atividades
        ]);
    }

    public function store(Request $request, Empresa $empresa){
        $correlacaoTribMunTribNac = new CorrelacaoTribMunTribNac();
        $dados = $request->all();

        $atividade = $this->atividadeModel->where('codigo_atividade', $request->cTribMun)->first();
        $dados['xTribMun'] = $atividade->descricao_atividade;
        $dados['empresa_id'] = $empresa->id;

        $existe = CorrelacaoTribMunTribNac::where('cTribMun', $request->cTribMun)
            ->where('cTribNac', $request->cTribNac)
            ->where('empresa_id', $empresa->id)
            ->first();

        if($existe){
            session()->flash('danger', 'Já existe um registro com os dados informado. Confira');
            return redirect()->route('codtrimun-codtribnac.index', $empresa);
        }

        $ins = $correlacaoTribMunTribNac->create($dados);
        session()->flash('success', 'Registro adicionado com sucesso!');
        
        return redirect()->route('codtrimun-codtribnac.index', [$empresa, $ins]);
    }
}
