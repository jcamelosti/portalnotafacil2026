<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\EmpresaCompartilhada;
use App\Models\Fatura;
use App\Models\License;
//use App\Models\NotaEmitida;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsuariosController extends Controller
{
    private $userModel;
    private $licencaModel;

    public function __construct(User $userModel, License $licencaModel)
    {
        $this->userModel = $userModel;
        $this->licencaModel = $licencaModel;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $campos = $request->all();
        $users = $this->userModel//->orderBy('name', 'asc')
        ->where(function($query) use($campos) {
            if(isset($campos['user_id']) && $campos['user_id'] != '0'){
                $query->where('id', $campos['user_id']);
            }
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);
        
        $usersList = $this->userModel->listEmpresasCountByUser();
        
        return view('admin.usuarios.index')->with([
            'users' => $users,
            'usersList' => $usersList,
            'pesquisa' => $campos
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->userModel->find($id);
        
        $empresas = $this->licencaModel
            ->select(
                'empresas.id',
                'empresas.razao_social',
                'empresas.cpf_cnpj',
                'licenses.validate as validate_licenca',
                'planos.id as plano_id',
                'planos.plano_nome',
                'users.name'
            )
            ->join('empresas', 'empresas.id', '=', 'licenses.empresa_id')
            ->join('planos', 'planos.id', '=', 'empresas.plano_id')
            ->join('users', 'users.id', '=', 'empresas.user_id')
            ->leftJoin('empresas_compartilhadas', 'empresas_compartilhadas.empresa_id', '=', 'empresas.id')
            ->where(function ($query) use ($id) {
                $query->where('empresas.user_id', $id)
                    ->orWhere('empresas_compartilhadas.solicitante_user_id', $id);
            })
            ->orderBy('licenses.validate', 'DESC')
            ->distinct()
            ->paginate(30);

        return view('admin.usuarios.show', compact('empresas', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->userModel->find($id);
        $name = $user->name;    
        //$user->delete();

        EmpresaCompartilhada::where('solicitante_user_id', $id)->delete();
        EmpresaCompartilhada::where('proprietario_user_id', $id)->delete();
        
        $empresas_usuario = Empresa::where('user_id', $id)->get();
        $faturas = Fatura::where('user_id', $id)->get();
        
        try {
            DB::transaction(function () use ($faturas, $empresas_usuario) {
                // Removendo faturas
                foreach ($faturas as $fat) {
                    $fat->items()->forceDelete();
                    $fat->forceDelete();
                }

                // Removendo empresas e relacionamentos
                foreach ($empresas_usuario as $emp) {

                    $emp->notasEmitidas()->forceDelete();
                    $emp->atividadesEmpresa()->forceDelete();
                    $emp->cnaes()->forceDelete();
                    //$emp->naturezaOperacoes()->forceDelete();
                    $emp->tomadores()->forceDelete();

                    $emp->licenca?->forceDelete();

                    $emp->forceDelete();
                }

            });

            $user->forceDelete();

            session()->flash('message', 'Usuário: ' . $name . ' foi removido.');
            return redirect()->route('admin.usuarios.index');
        } catch (\Throwable $e) {
            report($e);
            session()->flash('message', 'Usuário: ' . $name . ' não foi removido.');
            return redirect()->route('admin.usuarios.index');
        }
    }

    public function autenticar($userId){
        //if (Auth::user()->id == $userId) {
            Auth::loginUsingId($userId);
            session()->flash('message', 'Você está logado como: ' . Auth::user()->name);
        /*} else {
            session()->flash('danger', 'Você não possui essa autorização!');
        }*/

        //return redirect()->to('/c');
    }
}
