<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmpresaAtividade;
use Illuminate\Http\Request;

class EmpresaAtividadeController extends Controller
{
    private $atividadesModel;

    public function __construct(EmpresaAtividade $atividadesModel)
    {
        $this->atividadesModel = $atividadesModel;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $empresaId = $request->input('empresa-id');
        session()->put('empresa_id', $empresaId);
        
        $atividades = $this->atividadesModel
            ->where('empresa_id', $empresaId)
            ->paginate(5)
            ->appends($request->all());
        
        return view('admin.empresa-atividades.index')->with([
            'atividades' => $atividades,
            'empresaId' => $empresaId,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $empresaId = session()->get('empresa_id');
        $atividade = new EmpresaAtividade();
        $atividade->empresa_id = $empresaId;
                
        return view('admin.empresa-atividades.criar')->with([
            'atividade' => $atividade,
        ]);;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dados = $request->all();
        $dados['empresa_id'] = session()->get('empresa_id');
        $dados['aliquota'] = 0.0;
        $cnae = $this
            ->atividadesModel
            ->create($dados);

        if ($cnae->id != null) {
            session()->flash('message', 'Registro Inserido com Sucesso.');
        } else {
            session()->flash('danger', 'O Registro não pode ser Inserido. Tente Novamente.');
        }

        return redirect()->route('admin.empresa-atividades.edit', $cnae->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $atividade = $this->atividadesModel
            ->find($id);
        
        return view('admin.empresa-atividades.editar')->with([
            'atividade' => $atividade,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $dados = $request->all();
        $cnae = $this->atividadesModel->find($id);       
        $cnae->update($dados);

        session()->flash('message', 'Registro Atualizado com Sucesso.');
        return redirect()->route('admin.empresa-atividades.edit', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
