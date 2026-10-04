<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
//use App\Models\EmpresaCnae;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use stdClass;

class EmpresaCnaeController extends Controller
{
    private $cnaesModel;

    public function __construct(stdClass $cnaesModel)
    {
        $this->cnaesModel = $cnaesModel;
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
        
        $cnaes = $this->cnaesModel
            ->where('empresa_id', $empresaId)
            ->paginate(5)
            ->appends($request->all());
        
        return view('admin.empresa-cnaes.index')->with([
            'cnaes' => $cnaes,
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
        $cnae = new stdClass();
        $cnae->empresa_id = $empresaId;
        $cnae->principal = 2;

        return view('admin.empresa-cnaes.criar')->with([
            'cnae' => $cnae,
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
        $cnae = $this
            ->cnaesModel
            ->create($dados);

        if ($cnae->id != null) {
            session()->flash('message', 'Registro Inserido com Sucesso.');
        } else {
            session()->flash('danger', 'O Registro não pode ser Inserido. Tente Novamente.');
        }

        return redirect()->route('admin.empresa-cnaes.edit', $cnae->id);
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
        $cnae = $this->cnaesModel
            ->find($id);
                    
        return view('admin.empresa-cnaes.editar')->with([
            'cnae' => $cnae,
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
        $cnae = $this->cnaesModel->find($id);       
        $cnae->update($dados);

        session()->flash('message', 'Registro Atualizado com Sucesso.');
        return redirect()->route('admin.empresa-cnaes.edit', $id);
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
