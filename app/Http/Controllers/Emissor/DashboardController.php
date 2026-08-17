<?php

namespace App\Http\Controllers\Emissor;

use App\Business\NotasBO;
use App\Http\Controllers\Controller;
use App\Models\CreditoUser;
use App\Models\Empresa;
use App\Models\EmpresaCompartilhada;
use App\Models\License;
use App\Models\NotaEmitida;
use App\Models\PlanoVariacao;
use App\Utilitarios\Utilitarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    private $empresaModel;
    private $empresa;
    private $creditoUserModel;
    private $empresaCompartilhadaModel;
    private $variacaoPlanoModel;

    public function __construct(Empresa $empresaModel, CreditoUser $creditoUserModel, 
    EmpresaCompartilhada $empresaCompartilhadaModel, PlanoVariacao $variacaoPlanoModel){
        $this->empresaModel = $empresaModel;
        $this->creditoUserModel = $creditoUserModel;
        $this->empresaCompartilhadaModel = $empresaCompartilhadaModel;
        $this->variacaoPlanoModel = $variacaoPlanoModel;
    }

    public function index(){
        $sessionData = Session::get('empresa_selecionada');
        $this->empresa = $this->empresaModel->find($sessionData);

        if(is_null($this->empresa)){
            return redirect('/c');
        }

        $notasEmitidas = NotaEmitida::whereMonth('created_at', '=', date('m'))
            ->where('empresa_id', $this->empresa->id)
            ->count();          

        $notasCanceladas = NotaEmitida::whereMonth('created_at', '=', date('m'))
            ->where('cancelada', 1)
            ->where('empresa_id', $this->empresa->id)
            ->count();
        
        $notasEmitidasTotal = NotaEmitida::
            where('empresa_id', $this->empresa->id)
            ->count();          

        $notasCanceladasTotal = NotaEmitida::
            where('cancelada', 1)
            ->where('empresa_id', $this->empresa->id)
            ->count();

        $notas = NotaEmitida::where('empresa_id', $this->empresa->id)
            ->orderBy('num_nfse', 'desc')
            ->take(4)
            ->get();

        if($this->empresa->is_mei == 1){
            $this->empresa->regime_esp_tributacao = 5;
        }else{
            $dados = Utilitarios::consultarEmpresaCNPJ($this->empresa->cpf_cnpj);
        }
        $this->empresa->save();


        return view('dashboard_cliente')->with([
            'empresa' => $this->empresa,
            'notasEmitidas' => $notasEmitidas,
            'notasCanceladas' => $notasCanceladas,
            'notasEmitidasTotal' => $notasEmitidasTotal,
            'notasCanceladasTotal' => $notasCanceladasTotal,
            'notas' => $notas
        ]);
    }

    public function dashboard(){
        $credito = $this->creditoUserModel->where('user_id', Auth::user()->id)->first();
        $empresas = $this->empresaModel->where('user_id', Auth::user()->id)->get();
        $temLicencaValida = true;
        $license = null;

        $liberarCompartilhamentoEmpresa = $this->empresaCompartilhadaModel
            ->where('proprietario_user_id', Auth::user()->id)
            ->where('autorizado', 'N')->count();
        
        if($empresas->count() == 1){
            $license = License::whereDate('validate', '>=', DB::raw('CURDATE()'))
                ->where('empresa_id', $empresas->first()->id)->first();
            
            $temLicencaValida = true;
            $hj = date('Y-m-d');

            if(is_null($license)){
                $temLicencaValida = false;
            }else{
                if ($hj > $license->validate) {
                    $temLicencaValida = false;
                }
            }

            if(!$temLicencaValida){
                $license = License::whereDate('validate', '<=', DB::raw('CURDATE()'))
                    ->where('empresa_id', $empresas->first()->id)->first();
            }
        }

        /*$dados_faturamento = auth()->user()->cliente?->cpf_cnpj;
        if (
            !$dados_faturamento ||
            //empty($dados_faturamento->dt_nasc) ||
            empty($dados_faturamento->cpf_cnpj) ||
            empty($dados_faturamento->cep) ||
            empty($dados_faturamento->numero) ||
            empty($dados_faturamento->endereco) ||
            empty($dados_faturamento->bairro) ||
            empty($dados_faturamento->cidade_id) ||
            empty($dados_faturamento->telefone1)
        ) {
            return redirect()
                ->route('dados-faturamento.form')
                ->with('error', 'Complete seus dados cadastrais.');
        }*/

         //Plano do Reajuste
        $valorPlano = $this->variacaoPlanoModel->where('plano_id', 4)->get();
        $cores = [
            0 => 'indigo',
            1 => 'green',
            2 => 'orange',
            3 => 'blue',
            4 => 'red',
            5 => 'yellow'
        ];

        shuffle($cores);
        
        return view('dashboard')->with([
            'credito' => $credito,
            'temLicencaValida' => $temLicencaValida,
            'license' => $license,
            'empresa' => $empresas->first(),
            'quantCompartilhamentosSolicitados' => $liberarCompartilhamentoEmpresa,
            'variacaoPlanos' => $valorPlano,
            'cores' => $cores
        ]);
    }

    public function pagamentoRealizado(){
        return view('confirmacao-pagamento-realizado');
    }
}
