<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\License;
use App\Models\PlanoVariacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LicencaController extends Controller
{
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

    public function index(){
        $licencas = $this->licencaModel->paginate();

        return view('admin.licencas.index',compact('licencas'));
    }

      /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    public function licenca($id){
        $empresa = $this->empresaModel->find($id);
        $plano = $empresa->plano()->first();
        //$licencaDtValidade = $this->licencaModel->licenca($empresa->id);
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
        $l = $this->licencaModel
            ->where('empresa_id', $empresaId)
            //->whereDate('validate', '>=', DB::raw('CURDATE()'))
            //->orderBy('id', 'DESC')
            ->first();

        if(is_null($l)){
            $this->licencaModel->create([
                'empresa_id' => $empresaId,
                'validate' => Carbon::now()->subDay(1)->format('Y-m-d')
            ]);
        }
        
        /*$permitirAlterar = $l->updated_at->lte(
            Carbon::now()->subHours(2)
        );

        if (!$permitirAlterar) {
            session()->flash('danger', 'Este registro já foi alterado. Agora aguarde 2h para poder altera-lo novamente.');
            return redirect()->back();
        }*/

        $estaVencido = Carbon::parse($l->validate)->isPast();

        if ($estaVencido) {
            $dataRenovacao = Carbon::now()->format('Y-m-d');
            $dataVencimento = Carbon::createFromFormat('Y-m-d', $dataRenovacao)
                ->addDay($variacaoPlano->fator_vigencia)//->subDay(1)
                ->format('Y-m-d');
            
            $l->validate = $dataVencimento;
            $l->save();
        } else {
            $dataRenovacao = $l->validate;
            $dataVencimento = Carbon::createFromFormat('Y-m-d', $dataRenovacao)
                ->addDay($variacaoPlano->fator_vigencia)//->subDay(1)
                ->format('Y-m-d');

            $l->validate = $dataVencimento;
            $l->save();
        }        

        return redirect()->route('admin.empresas.index', ['empresa_id' => $empresaId]);
    }

    public function show(){
         /*$licVenc = $this->licencaModel
            ->select(
                'empresas.id',
                'empresas.razao_social',
                'empresas.cpf_cnpj',
                'licenses.validate as validate_licenca',
                'planos.id as plano_id',
                'planos.plano_nome',
                'users.name'
            )
            ->join('empresas','empresas.id','=','licenses.empresa_id')
            ->join('planos', 'planos.id', 'empresas.plano_id')
            ->join('users', 'users.id', 'empresas.user_id')
            ->whereDate('validate', '<=', Carbon::now())
            //->whereDate('validate', '<=', Carbon::now()->addDays(15))
            ->where('empresas.id', '<>', 1)
            ->orderBy('validate', 'ASC')
            ->get();*/

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
                $query->from('emitidas')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('emitidas.empresa_id', 'empresas.id');
            }, 'ultima_emissao')
            ->join('empresas', 'empresas.id', '=', 'licenses.empresa_id')
            ->join('planos', 'planos.id', '=', 'empresas.plano_id')
            ->join('users', 'users.id', '=', 'empresas.user_id')
            ->whereDate('validate', '<=', Carbon::now())
            //->whereDate('validate', '<=', Carbon::now()->addDays(15))
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
                $query->from('emitidas')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('emitidas.empresa_id', 'empresas.id');
            }, 'ultima_emissao')
            ->join('empresas','empresas.id','=','licenses.empresa_id')
            ->join('planos', 'planos.id', 'empresas.plano_id')
            ->join('users', 'users.id', 'empresas.user_id')
            ->whereDate('validate', '>', Carbon::now())
            ->whereDate('validate', '<=', Carbon::now()->addDays(15))
            ->where('empresas.id', '<>', 1)
            ->orderBy('validate', 'ASC')
            ->orderBy('ultima_emissao', 'ASC')
            ->get();
                
        return view('admin.licencas.show')->with(['licVenc' => $licVenc, 'licAVenc' => $licAVenc]);
    }
}
