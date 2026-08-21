<?php

namespace App\Http\Controllers\Emissor;

use App\Business\NotasBO;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotaCreateRequest;
use App\Models\Certificado;
use App\Models\CnaeLc;
use App\Models\CodigoTribNacional;
use App\Models\CorrelacaoTribMunTribNac;
use App\Models\Empresa;
use App\Models\EmpresaAtividade;
use App\Models\EmpresaCnae;
use App\Models\EmpresaNbs;
use App\Models\ListaServico;
use App\Models\Municipio;
use App\Models\Nbs;
//use App\Models\NotaEmitida;
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
    private $empresaModel;
    private $tomadorModel;
    private $estadoModel;
    private $municipioModel;
    private $nbsModel;
    private $atividadeModel;

    
    public function __construct(Empresa $empresaModel, Tomador $tomadorModel, 
        Uf $estadoModel, Municipio $municipioModel,
        EmpresaAtividade $atividadeModel,
        Nbs $nbsModel
    ){
        //$this->notaBO = NotasBO::newInstance();
        $this->empresaModel = $empresaModel;
        $this->tomadorModel = $tomadorModel;
        //$this->notasModel = $notasModel;
        $this->estadoModel = $estadoModel;
        $this->municipioModel = $municipioModel;
        $this->atividadeModel = $atividadeModel;
        $this->nbsModel = $nbsModel;
    }

    public function index(){
       dd('Index');
    }

    public function create(){
        $tomador = $this->tomadorModel->find(Session::get('tomador_selecionado'));
        $empresa = $this->empresaModel->with(['atividadesEmpresa'])
            ->find(Session::get('empresa_selecionada'));
        
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
    
        $atividades = $this->atividadeModel->atividadesByCTribMunList($empresa->id);
        $atividade = $this->atividadeModel
            ->where('empresa_id', $empresa->id)
            ->where('id', $empresa->empresa_atividade_id)->first();

        $data_competencia = date('Y-m-d');

        $cod_trib_nac = [null => 'Selecione o Código de Tributação Nacional'] + CorrelacaoTribMunTribNac::select(
                'cTribNac',
                DB::raw("concat(cTribNac, ' - ', IFNULL(xTribNac, '')) as field1")
            )
            ->where('cTribMun', $atividade->codigo_atividade)
            ->where('empresa_id', $empresa->id)
            ->orderBy('cTribMun', 'asc')
            ->pluck('field1', 'cTribNac')
            ->all();
        
        return view('emissor.create', [
            'data_competencia' => $data_competencia,
            'tomador' => $tomador,
            'empresa' => $empresa,
            'estados' => $estados,
            'uf_id' => $uf_id,
            'cidades' => $cidades,
            'dados_cadastrais' => $dadosCadastrais,
            'atividades' => $atividades,
            'cod_trib_nac' => $cod_trib_nac,
            'atividade' => $atividade
        ]);
    }

    public function store(Request $request){
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

    /* Pesquisas */
    public function obterTributacaoNacionalPorAtividadeMun(Request $request)
    {                
        $dados = CorrelacaoTribMunTribNac::query()->
        select(
                'cTribNac',
                DB::raw("concat(cTribNac, ' - ', IFNULL(xTribNac, '')) as descricao")
            )
            ->where('cTribMun', $request->cTribMun)
            ->where('empresa_id', Session::get('empresa_selecionada'))
            ->orderBy('cTribMun', 'asc')
            ->get();
        
        return response()->json($dados,200,[],JSON_UNESCAPED_UNICODE);
    }  

    public function obterNbs(Request $request){
        $corrTrib = CorrelacaoTribMunTribNac::where('cTribNac', $request->cTribNac)
            ->where('empresa_id', Session::get('empresa_selecionada'))
            ->first();

        $dados = EmpresaNbs::query()->
        select(
                'codigo',
                DB::raw("concat(codigo, ' - ', IFNULL(descricao, '')) as descricao")
            )
            ->where('correlaca_trib_id', $corrTrib->id)
            ->where('empresa_id', Session::get('empresa_selecionada'))
            ->orderBy('codigo', 'asc')
            ->get();
        
        return response()->json($dados,200,[],JSON_UNESCAPED_UNICODE);
    }
}
