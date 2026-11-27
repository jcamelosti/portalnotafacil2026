<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\User;
use App\Services\Usuarios\ExclusaoUsuarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Rules\Password;

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
     * Listagem com filtros por usuário, texto (nome/e-mail) e perfil.
     */
    public function index(Request $request)
    {
        $campos = $request->only(['user_id', 'busca', 'perfil']);

        $users = $this->userModel
            ->withCount(['empresas', 'empresasCompartilhadas'])
            ->when(!empty($campos['user_id']) && $campos['user_id'] != '0', function ($q) use ($campos) {
                $q->where('id', $campos['user_id']);
            })
            ->when(!empty($campos['busca']), function ($q) use ($campos) {
                $termo = '%' . trim($campos['busca']) . '%';
                $q->where(function ($sub) use ($termo) {
                    $sub->where('name', 'like', $termo)->orWhere('email', 'like', $termo);
                });
            })
            ->when(isset($campos['perfil']) && in_array($campos['perfil'], ['0', '1'], true), function ($q) use ($campos) {
                $q->where('is_admin', $campos['perfil']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($campos);

        $usersList = $this->userModel->listEmpresasCountByUser();

        return view('admin.usuarios.index')->with([
            'users' => $users,
            'usersList' => $usersList,
            'pesquisa' => $campos,
        ]);
    }

    public function create()
    {
        return view('admin.usuarios.criar', ['user' => new User()]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate($this->regras(), [], $this->atributos());

        $user = new User();
        $user->forceFill([
            'name' => $dados['name'],
            'email' => mb_strtolower($dados['email']),
            'password' => Hash::make($dados['password']),
            'is_admin' => $request->boolean('is_admin') ? '1' : '0',
            'email_verified_at' => $request->boolean('email_verificado') ? now() : null,
        ])->save();

        session()->flash('message', 'Usuário <strong>' . e($user->name) . '</strong> cadastrado com sucesso.');
        return redirect()->route('admin.usuarios.index');
    }

    /**
     * Detalhes do usuário com as empresas próprias e compartilhadas.
     */
    public function show($id)
    {
        $user = $this->userModel->findOrFail($id);

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

    public function edit($id)
    {
        $user = $this->userModel->findOrFail($id);
        return view('admin.usuarios.editar', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = $this->userModel->findOrFail($id);
        $dados = $request->validate($this->regras($user), [], $this->atributos());

        $isAdmin = $request->boolean('is_admin') ? '1' : '0';
        if ($user->id === Auth::id() && $isAdmin !== '1') {
            return back()->withInput()->withErrors(['is_admin' => 'Você não pode remover o seu próprio acesso de administrador.']);
        }

        $user->forceFill([
            'name' => $dados['name'],
            'email' => mb_strtolower($dados['email']),
            'is_admin' => $isAdmin,
        ]);

        if (!empty($dados['password'])) {
            $user->password = Hash::make($dados['password']);
        }

        if ($request->boolean('email_verificado')) {
            $user->email_verified_at = $user->email_verified_at ?? now();
        } else {
            $user->email_verified_at = null;
        }

        $user->save();

        session()->flash('message', 'Usuário <strong>' . e($user->name) . '</strong> atualizado com sucesso.');
        return redirect()->route('admin.usuarios.index');
    }

    /**
     * Tela de confirmação da exclusão, com o resumo do que será removido.
     */
    public function confirmarExclusao($id, ExclusaoUsuarioService $exclusao)
    {
        $user = $this->userModel->findOrFail($id);

        if ($user->id === Auth::id()) {
            session()->flash('danger', 'Você não pode excluir o seu próprio usuário.');
            return redirect()->route('admin.usuarios.index');
        }

        return view('admin.usuarios.excluir', [
            'user' => $user,
            'resumo' => $exclusao->resumo($user),
        ]);
    }

    /**
     * Exclui o usuário e todos os registros relacionados.
     * Exige que o e-mail do usuário seja digitado como confirmação.
     */
    public function destroy(Request $request, $id, ExclusaoUsuarioService $exclusao)
    {
        $user = $this->userModel->findOrFail($id);

        if ($user->id === Auth::id()) {
            session()->flash('danger', 'Você não pode excluir o seu próprio usuário.');
            return redirect()->route('admin.usuarios.index');
        }

        if (mb_strtolower(trim((string) $request->input('confirmacao'))) !== mb_strtolower($user->email)) {
            return redirect()->route('admin.usuarios.excluir', $user->id)
                ->withErrors(['confirmacao' => 'O e-mail digitado não confere. A exclusão não foi realizada.']);
        }

        $name = $user->name;

        try {
            $exclusao->excluir($user);
            session()->flash('message', 'Usuário <strong>' . e($name) . '</strong> e todos os seus registros foram removidos.');
        } catch (\Throwable $e) {
            report($e);
            session()->flash('danger', 'Usuário <strong>' . e($name) . '</strong> não foi removido. Nenhum dado foi apagado. Erro: ' . e($e->getMessage()));
        }

        return redirect()->route('admin.usuarios.index');
    }

    public function autenticar($userId)
    {
        // Funcionalidade desativada.
    }

    private function regras(?User $user = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id ?? null),
            ],
            'password' => $user
                ? ['nullable', 'string', new Password(), 'confirmed']
                : ['required', 'string', new Password(), 'confirmed'],
        ];
    }

    private function atributos(): array
    {
        return [
            'name' => 'nome',
            'email' => 'e-mail',
            'password' => 'senha',
        ];
    }
}
