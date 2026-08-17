<?php

namespace App\Http\Controllers\Tomadores;

use App\Http\Controllers\Controller;
use App\Http\Requests\TomadorCreateRequest;
use App\Http\Requests\TomadorUpdateRequest;
use App\Models\Empresa;
use App\Models\Municipio;
use App\Models\Tomador;
use App\Models\Uf;
use App\Utilitarios\Utilitarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TomadoresController extends Controller
{
    private $tomadorModel;
    private $estadoModel;
    private $municipioModel;
   
    public function __construct(Tomador $tomadorModel, Uf $estadoModel, Municipio $municipioModel)
    {
        $this->tomadorModel = $tomadorModel;
        $this->estadoModel = $estadoModel;
        $this->municipioModel = $municipioModel;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campos = request()->all();
        
        $tomadores = $this->tomadorModel
            ->orderBy('id', 'desc')
            ->where('empresa_id',  Session::get('empresa_selecionada'))
            ->where(function($query) use($campos) {
                if(isset($campos['tomador_id']) && $campos['tomador_id'] != '0'){
                    $query->where('id', $campos['tomador_id']);
                }
            })
            ->paginate(4);

        $tomadoresList = $this->tomadorModel
            ->tomadoresList(Session::get('empresa_selecionada'));
    
        return view('tomadores.index')->with([
            'tomadores' => $tomadores,
            'tomadoresList' => $tomadoresList
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tomador = new Tomador();
        $tomador->cidade_id = 0;

        $referer = request()->headers->get('referer');

        $estados = $this->estadoModel->getListaEstados();
        $cidades = $this->municipioModel->municipios();

        $dados_busca_cnpj = Session::has('nova_empresa');

        if ($referer && str_contains($referer, '/area-cliente/tomadores')) {
            $dados_busca_cnpj = false;            
        }

        if($dados_busca_cnpj){
            $dadosEmpresa = Session::get('nova_empresa');
            if(!empty($dadosEmpresa)){
                $tomador->fill($dadosEmpresa);
                $estado = $this->estadoModel->where('sigla', $dadosEmpresa['uf'])->first();
            }else{
                session()->flash('danger', 'Não foi possível consultar o CNPJ ou CPF informado. Insira os Dados Manualmente');
            }
        }
        
        return view('tomadores.criar')->with([
            'empresa' => $tomador,
            'estados' => $estados,
            'cidades' => $cidades,
            'uf_id' => !empty($estado) ? $estado->id : 9
        ]);
    }

    public function create_ext()
    {
        $tomador = new Tomador();
        $tomador->cidade_id = '99999';
        $estados = [28 => "Exterior"];
        $cidades = $this->municipioModel->municipios(28);
              
        return view('tomadores.criar_exterior')->with([
            'empresa' => $tomador,
            'estados' => $estados,
            'cidades' => $cidades,
            'uf_id' => !empty($estado) ? $estado->id : 9
        ]);
    }

    public function buscarDadosEmpresa(){
        if(request()->method() == 'POST'){
            $dadosEmpresa = Utilitarios::consultarEmpresaCNPJ(request()->get('cpf_cnpj'));
            
            Session::put('nova_empresa', $dadosEmpresa);
            return redirect()->route('tomadores.create');
        }
        return view('tomadores.adicionar-tomador');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TomadorCreateRequest $request)
    {
        $dados = $request->all();
        
        $qtd_empresas = Empresa::where('user_id', auth()->user()->id)->count();

        if($qtd_empresas == 1){
            $empresa_user = Empresa::where('user_id', auth()->user()->id)->first();
            $dados['empresa_id'] = $empresa_user->id;
        }else{
            if(Session::has('empresa_selecionada')){
                $dados['empresa_id'] = Session::get('empresa_selecionada');
            }else{
                session()->flash('danger', 'Nenhuma empresa selecionada. Selecione uma empresa para cadastrar o tomador.');
                return redirect()->route('empresas.selecionar-empresa');
            }
        }
                     
        $tomador = $this->tomadorModel->where('empresa_id', $dados['empresa_id'])
            ->where('cpf_cnpj', Utilitarios::limparCpfCnpj($dados['cpf_cnpj']))
            //->where('inscricao_municipal', $dados['inscricao_municipal'])
            ->first();
            
        if(!is_null($tomador)){
            session()->flash('danger', 'Tomador já Cadastrado. Caso seja o mesmo CNPJ informe a Inscrição Municipal Diferente.');
            return redirect()->back()->withInput($dados);
        }

        /*if(empty($dados['numero']) || is_null($dados['numero'])){
            $dados['numero'] = 'S/N';
        }*/

        $tomador = $this
            ->tomadorModel
            ->create($dados);

        if ($tomador->id != null) {
            session()->flash('message', 'Registro Inserido com Sucesso.');
        } else {
            session()->flash('danger', 'O Registro não pode ser Inserido. Tente Novamente.');
        }

        Session::forget('nova_empresa');

        return redirect()->route('tomadores.edit', $tomador->id);
    }


    public function store_ext(Request $request)
    {
        $dados = $request->all();
        $dados['empresa_id'] = Session::get('empresa_selecionada');
        $dados['cpf_cnpj'] = "NAO_INFORMADO";
        $dados['cep'] = '00000000';
        $dados['bairro'] = "NAO_INFORMADO";

        $tomador = $this
            ->tomadorModel
            ->create($dados);

        if ($tomador->id != null) {
            session()->flash('message', 'Registro Inserido com Sucesso.');
        } else {
            session()->flash('danger', 'O Registro não pode ser Inserido. Tente Novamente.');
        }

        return redirect()->route('tomadores.edit', $tomador->id);
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
        $tomador = $this->tomadorModel
            ->where('empresa_id', Session::get('empresa_selecionada'))
            ->find($id);

        if(empty($tomador)){
            session()->flash('message', 'Empresa não encontrada.');
            return redirect()->route('tomadores.index');
        }

        if($tomador->cidade_id != "99999"){
            $estados = $this->estadoModel->getListaEstados();
        }else{
            $estados = [28 => "Exterior"];
        }       

        $cidade = $tomador->cidade()->first();
        $uf_id = $tomador->cidade()->first()->estado()->first()->id;
        $cidades = $this->municipioModel->municipios($uf_id);

        return view('tomadores.editar')->with([
            'estados' => $estados,
            'empresa' => $tomador,
            'tomador' => $tomador,
            'uf_id' => $uf_id,
            'cidade' => $cidade,
            'cidades' => $cidades
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TomadorUpdateRequest $request, $id)
    {
        $dados = $request->all();
        $tomador = $this->tomadorModel->find($id);
        
        if(!empty($tomador) && $tomador->empresa_id != Session::get('empresa_selecionada')){
            session()->flash('message', 'Você não pode editar esse Tomador.');
            return redirect()->route('tomadores.edit', $id);
        }

        /*if(empty($dados['numero']) || is_null($dados['numero'])){
            $dados['numero'] = 'S/N';
        }*/
       
        $tomador->update($dados);

        session()->flash('message', 'Registro Atualizado com Sucesso.');

        return redirect()->route('tomadores.edit', $id);
    }

    public function update_ext(Request $request, $id)
    {
        $dados = $request->all();
        $dados['cpf_cnpj'] = "NAO_INFORMADO";

        $tomador = $this->tomadorModel->find($id);
        
        if(!empty($tomador) && $tomador->empresa_id != Session::get('empresa_selecionada')){
            session()->flash('message', 'Você não pode editar esse Tomador.');
            return redirect()->route('tomadores.edit', $id);
        }

        /*if(empty($dados['numero']) || is_null($dados['numero'])){
            $dados['numero'] = 'S/N';
        }*/
       
        $tomador->update($dados);

        session()->flash('message', 'Registro Atualizado com Sucesso.');

        return redirect()->route('tomadores.edit', $id);
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

    public function selecionarTomador(Request $request){
        if($request->method() != 'POST'){
            $tomadoresList = $this->tomadorModel
                ->tomadoresList(Session::get('empresa_selecionada'));
            
            return view('tomadores.selecionar_tomador',[
                'tomadoresList' => $tomadoresList
            ]);
        }else{
            Session::put('tomador_selecionado', $request->get('tomador_id'));
            return redirect()->route('nota.emitir');
        }
    }

    public function buscar(Request $request)
    {
        $q = $request->q;
        
        $empresas = Tomador::query()
            ->when($q, function($query) use ($q){
                $query->where('empresa_id', Session::get('empresa_selecionada'));
                $query->where('razao_social','like',"%{$q}%")
                      ->orWhere('cpf_cnpj','like',"%{$q}%");
            })
            ->limit(10)
            ->get(['id','razao_social','cpf_cnpj']);

         $dados = $empresas->map(function($empresa){
            return [
                'id'   => $empresa->id,
                'razao_social' => $empresa->razao_social,
                'cpf_cnpj' => $empresa->cpf_cnpj
            ];

        });

        return response()->json($dados,200,[],JSON_UNESCAPED_UNICODE);
    }    
}
