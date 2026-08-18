<?php

namespace App\Http\Controllers\Emissor;

use App\Business\NotasBO;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotaCreateRequest;
use App\Models\Certificado;
use App\Models\CnaeLc;
use App\Models\CodigoTribNacional;
use App\Models\Empresa;
use App\Models\EmpresaAtividade;
use App\Models\EmpresaCnae;
use App\Models\ListaServico;
use App\Models\Municipio;
use App\Models\Nbs;
use App\Models\NotaEmitida;
use App\Models\Tomador;
use App\Models\Uf;
use App\Traits\IssnetTrait;
use App\Utilitarios\Utilitarios;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use NFePHP\NFSe\Models\Issnet\RpsClass;

use function PHPUnit\Framework\isNull;

class NotaController extends Controller
{
    use IssnetTrait;

    private $notaBO;
    private $empresaModel;
    private $tomadorModel;
    //private $notasModel;
    private $estadoModel;
    private $municipioModel;
    private $nfseModel;
    private $cnaeModel;
    private $atividadeModel;
    private $listaServicoModel;
    private $nbsModel;
    
    public function __construct(Empresa $empresaModel, Tomador $tomadorModel, 
        Uf $estadoModel, Municipio $municipioModel,
        NotaEmitida $nfseModel, EmpresaCnae $cnaeModel, EmpresaAtividade $atividadeModel,
        ListaServico $listaServicoModel, Nbs $nbsModel
    ){
        $this->notaBO = NotasBO::newInstance();
        $this->empresaModel = $empresaModel;
        $this->tomadorModel = $tomadorModel;
        //$this->notasModel = $notasModel;
        $this->estadoModel = $estadoModel;
        $this->municipioModel = $municipioModel;
        $this->nfseModel = $nfseModel;
        $this->cnaeModel = $cnaeModel;
        $this->atividadeModel = $atividadeModel;
        $this->listaServicoModel = $listaServicoModel;
        $this->nbsModel = $nbsModel;
    }

    public function index(){
       dd('Index');
    }

    public function create(){
        $tomador = $this->tomadorModel->find(Session::get('tomador_selecionado'));
        $empresa = $this->empresaModel->with(['atividadesEmpresa'])
            ->find(Session::get('empresa_selecionada'));

        $permiteDescontoCond = $empresa->permite_desc_cond == 2 ? 'display: none;' : '';
        $permiteDescontoInc = $empresa->permite_desc_incond == 2 ? 'display: none;' : '';
        $permiteDeducao = $empresa->permite_deducao == 2 ? 'display: none;' : '';

        $estados = $this->estadoModel->getListaEstados();
        $uf_id = $empresa->cidade()->first()->uf_id;
        $cidades = $this->municipioModel->municipios($uf_id);

        $dadosCadastrais = json_decode($empresa->dados_cadastrais);

        $verificar_validade_atividade = $empresa->atividadesEmpresa()
            ->where('id', $empresa->empresa_atividade_id)
            ->where(function ($query) {
                $query->whereNull('vigencia_final')
                    ->orWhere('vigencia_final', '>=', now());
            })
            ->first();
        
        if(!is_null($verificar_validade_atividade)){
            if (!isNull($verificar_validade_atividade->vigencia_final) && Carbon::parse($verificar_validade_atividade->vigencia_final)->isPast()) {
                session()->flash('danger', 'Opss! A vigência da Atividade no Municipio('. $verificar_validade_atividade->descricao_atividade .') selecionada está Expirada!');
                return redirect()->route('empresas.edit', $empresa->id);
            }
        }

        $certificadoCliente = Certificado::where('empresa_id', $empresa->id)
            ->first();

        if(is_null($certificadoCliente)){
            session()->flash('danger', 'Opss! A empresa não possui um Certificado Digital válido cadastrado.');
            return redirect()->route('empresas.edit', $empresa->id);
        }
    
        //atividades
        $atividades = $this->atividadeModel->atividadesList($empresa->id);

        $data_competencia = date('Y-m-d');

        $cod_trib_nac = [''=>'Selecione o Código De Tributação'] + CodigoTribNacional::select(
                'codigo_tributacao',
                DB::raw("concat(codigo_tributacao, ' - ', IFNULL(descricao, '')) as field1")
            )
            ->orderBy('codigo_tributacao', 'asc')
            ->pluck('field1', 'codigo_tributacao')
            ->all();
        
        return view('emissor.create', [
            'data_competencia' => $data_competencia,
            'tomador' => $tomador,
            'empresa' => $empresa,
            'permiteDescontoInc' => $permiteDescontoInc,
            'permiteDescontoCond' => $permiteDescontoCond,
            'permiteDeducao' => $permiteDeducao,
            'estados' => $estados,
            'uf_id' => $uf_id,
            'cidades' => $cidades,
            'dados_cadastrais' => $dadosCadastrais,
            'atividades' => $atividades,
            'cod_trib_nac' => $cod_trib_nac
        ]);
    }

    public function store(NotaCreateRequest $request){
        try{
            $empresaSessao = request()->session()->get('empresa_selecionada');
            $empresaSessao = $this->empresaModel->find($empresaSessao);
            
            $dados = $request->all();
            //$dados = $this->notaBO->tratarDados($dados);

            
            dd($dados);
        }catch(\Exception $e){
            DB::insert(
                'INSERT INTO internal_logs (empresa_id, description) VALUES (?, ?)',
                [
                    $empresaSessao->id,
                    $e->getMessage()
                ]
            );
            session()->flash('danger', 'Opss! Houve falha na Emissão da NFS-e');
        }

        return redirect()->route('nota.index');
    }
}
