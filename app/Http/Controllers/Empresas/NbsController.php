<?php

namespace App\Http\Controllers\Empresas;

use App\Http\Controllers\Controller;
use App\Models\CorrelacaoTribMunTribNac;
use App\Models\Empresa;
use App\Models\EmpresaNbs;
use App\Models\Nbs;
use Illuminate\Http\Request;

class NbsController extends Controller
{
    private $correlacaoTribMunTribNacModel;
    private $nbsModel;

    public function __construct(
        CorrelacaoTribMunTribNac $correlacaoTribMunTribNacModel, Nbs $nbsModel
    )
    {
        $this->correlacaoTribMunTribNacModel = $correlacaoTribMunTribNacModel;
        $this->nbsModel = $nbsModel;
    }

    public function index(Request $request, Empresa $empresa){
        $query = EmpresaNbs::porEmpresa($empresa->id);

        $empresasNbss = $query
            ->paginate(15)
            ->withQueryString();

        return view('empresas.empresa-nbs.index')->with([
            'empresasNbss' => $empresasNbss,
            'empresa' => $empresa
        ]);
    }

    public function edit(Empresa $empresa, EmpresaNbs $empresaNbs){
        $tributacaoNacList = $this->correlacaoTribMunTribNacModel->listCorrelacao($empresa->id);
        $nbsList = $this->nbsModel->getListaNbsPorCodigo();

        return view('empresas.empresa-nbs.edit')->with([
            'empresa' => $empresa,
            'empresaNbs' => $empresaNbs,
            'tributacaoNacList' => $tributacaoNacList,
            'nbsList' => $nbsList
        ]);
    }

    public function update(Request $request, Empresa $empresa, EmpresaNbs $empresaNbs){
        $dados = $request->all();
        
        $nbs = $this->nbsModel->where('codigo', $dados['nbs_id'])->first();
        
        $empresaNbs->correlaca_trib_id = $dados['correlaca_trib_id'];
        $empresaNbs->codigo = $nbs->codigo;
        $empresaNbs->descricao = $nbs->descricao;

        $empresaNbsExiste = EmpresaNbs::where('empresa_id', $empresa->id)
            ->where('codigo', $nbs->codigo)
            ->where('id', '<>', $empresaNbs->id)
            ->first();
    
        if($empresaNbsExiste){
            session()->flash('danger', 'Já existe um registro com os dados informado. Confira');
            return redirect()->route('empresa-nbs.edit', [$empresa, $empresaNbsExiste]);
        }
        
        $empresaNbs->save();

        session()->flash('message', 'Registro Alterado com Sucesso.');

        return redirect()->route('empresa-nbs.index', $empresa);
    }

    public function create(Empresa $empresa){
        $tributacaoNacList = $this->correlacaoTribMunTribNacModel->listCorrelacao($empresa->id);
        $nbsList = $this->nbsModel->getListaNbsPorCodigo();

        return view('empresas.empresa-nbs.create')->with([
            'empresa' => $empresa,
            'empresaNbs' => new EmpresaNbs(),
            'tributacaoNacList' => $tributacaoNacList,
            'nbsList' => $nbsList
        ]);
    }

    public function store(Request $request, Empresa $empresa){
        $dados = $request->all();

        $empresaNbsExiste = EmpresaNbs::where('empresa_id', $empresa->id)
            ->where('correlaca_trib_id', $dados['correlaca_trib_id'])
            ->where('codigo', '<>', $dados['nbs_id'])
            ->first();
    
        if($empresaNbsExiste){
            session()->flash('danger', 'Já existe um registro com os dados informado. Confira');
            return redirect()->route('empresa-nbs.edit', [$empresa, $empresaNbsExiste]);
        }

        $nbs = $this->nbsModel->where('codigo', $dados['nbs_id'])->first();

        $empresaNbs = [
            'empresa_id' => $empresa->id,
            'correlaca_trib_id' => $dados['correlaca_trib_id'],
            'codigo' => $nbs->codigo,
            'descricao' => $nbs->descricao,
        ];

        $ins = EmpresaNbs::create($empresaNbs);
        session()->flash('success', 'Registro adicionado com sucesso!');
        
        return redirect()->route('empresa-nbs.edit', [$empresa, $ins]);
    }
}
