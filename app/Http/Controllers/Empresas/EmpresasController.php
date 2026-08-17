<?php

namespace App\Http\Controllers\Empresas;

use App\Http\Controllers\Controller;
use App\Models\Certificado;
use App\Models\CnaeLc;
use App\Models\Empresa;
use App\Models\EmpresaAtividade;
use App\Models\EmpresaCnae;
use App\Models\EmpresaCompartilhada;
use App\Models\License;
use App\Models\ListaServico;
use App\Models\Municipio;
use App\Models\Nbs;
use App\Models\Uf;
use App\Traits\IssnetTrait;
use App\Utilitarios\Utilitarios;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use JCamelo\NfseNacionalLib\Services\NFSeService;

class EmpresasController extends Controller
{
    use IssnetTrait;

    private $empresaModel;
    private $estadoModel;
    private $municipioModel;
    private $listaServicoModel;
    private $cnaeModel;
    private $atividadeModel;
    private $licenseModel;
    private $certificadoModel;
    private $empresaCompartilhadaModel;
    private $nbsModel;
    private NFSeService $nfse;

    public function __construct(
        NFSeService $nfse,
        Empresa $empresaModel, Uf $estadoModel, 
        Municipio $municipioModel, ListaServico $listaServicoModel,
        EmpresaCnae $cnaeModel, EmpresaAtividade $atividadeModel,
        License $licenseModel, Certificado $certificadoModel, EmpresaCompartilhada $empresaCompartilhadaModel, Nbs $nbsModel
    )
    {
        $this->empresaModel = $empresaModel;
        $this->estadoModel = $estadoModel;
        $this->municipioModel = $municipioModel;
        $this->listaServicoModel = $listaServicoModel;
        $this->cnaeModel = $cnaeModel;
        $this->atividadeModel = $atividadeModel;
        $this->licenseModel = $licenseModel;
        $this->certificadoModel = $certificadoModel;
        $this->empresaCompartilhadaModel = $empresaCompartilhadaModel;
        $this->nbsModel = $nbsModel;
        $this->nfse = $nfse;
    }

    public function json(){
        $empresasList = $this->empresaModel
            ->select(['id', 'razao_social'])
            ->where('user_id', Auth::user()->id)->get();
        return response()->json( $empresasList );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userId = Auth::user()->id;

        $empresasList = $this->empresaModel
            ->empresasList(Auth::user()->id);
         
        $campos = $request->all();

        $empresas = $this->empresaModel
            ->orderBy('id', 'desc')
            ->where(function($query) use($campos) {
                if(isset($campos['empresa_id']) && $campos['empresa_id'] != '0'){
                    $query->where('id', $campos['empresa_id']);
                }
            })
            /*->where('user_id', $userId )
             // OU empresas compartilhadas com ele
            ->orWhereIn('id', function($sub) use ($userId) {
                $sub->select('empresa_id')
                    ->from('empresas_compartilhadas')
                    ->where('solicitante_user_id', $userId)
                    ->where('autorizado', 'S');
            })*/
            ->where(function($q) use ($userId) {
                $q->where('user_id', $userId)
                ->orWhereExists(function($sub) use ($userId) {
                    $sub->select(DB::raw(1))
                        ->from('empresas_compartilhadas')
                        ->whereColumn('empresas_compartilhadas.empresa_id', 'empresas.id')
                        ->where('empresas_compartilhadas.solicitante_user_id', $userId)
                        ->where('empresas_compartilhadas.autorizado', 'S');
                });
            })
            ->paginate();
        
        return view('empresas.index')->with([
            'empresas' => $empresas,
            'empresasList' => $empresasList,
            'user_id' => $userId
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $referer = request()->headers->get('referer');
       
        $estados = $this->estadoModel->getListaEstados();
        $cidades = $this->municipioModel->municipiosComEndPoint();
        $servicos = $this->listaServicoModel->getListaServicos();
        $uf_id = 9;
        $cidades = $this->municipioModel->municipiosComEndPoint($uf_id);
        $cnaes = [];
        $atividades = [];
        $estado = null;
        $empresa = new Empresa();
        //$empresa->cidade_id = 0;
        $regimeEspecialTributacaoList = $this->empresaModel->getRegimeEspecialTributacao();
        
        $dados_busca_cnpj = Session::has('nova_empresa_prest');

        if ($referer && str_contains($referer, '/area-cliente/empresas')) {
            $dados_busca_cnpj = false;            
        }

        if($dados_busca_cnpj){
            $dadosEmpresa = Session::get('nova_empresa_prest');
            if(!empty($dadosEmpresa)){
                $empresa->fill($dadosEmpresa);
                $estado = $this->estadoModel->where('sigla', $dadosEmpresa['uf'])->first();
            }else{
                session()->flash('danger', 'Não foi possível consultar o CNPJ informado. Insira os Dados Manualmente');
            }
        }

        return view('empresas.criar')->with([
            'empresa' => $empresa,
            'uf_id' => !empty($estado) ? $estado->id : 9,
            'estados' => $estados,
            'cidades' => $cidades,
            'servicos' => $servicos,
            'atividades' => $atividades,
            'cnaes' => $cnaes,
            'reg_esp_trib' => $regimeEspecialTributacaoList,
            'estado' => $estado
        ]);
    }

    public function buscarDadosEmpresa(){
        if(request()->method() == 'POST'){
            $dadosEmpresa = Utilitarios::consultarEmpresaCNPJ(request()->get('cpf_cnpj'));
            Session::put('nova_empresa_prest', $dadosEmpresa);
            
            return redirect()->route('empresas.create');
        }
        
        return view('empresas.adicionar-empresa');
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
        $dados['user_id'] = Auth::user()->id;

        $empresaExiste = $this->empresaModel->where('cpf_cnpj', preg_replace('/[^[:alnum:]_]/', '', $dados['cpf_cnpj']))->first();

        //blessed
        if (collect([56,78,79,86])->contains(auth()->id())) {
            $dados['plano_id'] = 1;
        }

        switch($dados['cidade_id']):
            case '5208707'://goiania
                $dados['serie_nota'] = 1;
                break;
            case '5201405'://aparecidada de goiania
                $dados['serie_nota'] = 9;
                break;
            case '3301702'://duque de caxias
                $dados['serie_nota'] = 1;
                break;
            case '3543402'://Ribeirão Preto
                $dados['serie_nota'] = 1;
                break;
            default:
                $dados['serie_nota'] = 8;
                break;
        endswitch;

        if(!is_null($empresaExiste)){
            session()->flash('info', 'Não foi possível adicionar a Empresa. A empresa já encontra-se cadastrada no sistema.');
            return redirect()->back();
        }

        $empresa = $this
            ->empresaModel
            ->create($dados);

        if ($empresa->id != null) {
            License::create([
                'empresa_id' => $empresa->id,
                'validate' => Carbon::now()->subDay(1)->format('Y-m-d')
            ]);            
            
            Utilitarios::sendMessage('Nova empresa cadastrada: ' . $empresa->razao_social . ' - CNPJ: ' . $empresa->cpf_cnpj . ' - Usuário: ' . auth()->user()->id . '  ' .auth()->user()->name);

            session()->flash('message', 'Registro Inserido com Sucesso.');
        } else {
            session()->flash('message', 'Não foi possível adicionar a Empresa. Tente novamente');
            return redirect()->back();
        }

        return redirect()->route('empresas.edit', $empresa->id);
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
        $userId = Auth::user()->id;
        
        $empresa = $this->empresaModel
            ->with(['cnaes'])
            //->where('user_id', $userId)
             // OU empresas compartilhadas com ele
            /*->orWhereIn('id', function($sub) use ($userId) {
                $sub->select('empresa_id')
                    ->from('empresas_compartilhadas')
                    ->where('solicitante_user_id', $userId)
                    ->where('autorizado', 'S');
            })*/
            ->where(function($q) use ($userId) {
                $q->where('user_id', $userId)
                ->orWhereExists(function($sub) use ($userId) {
                    $sub->select(DB::raw(1))
                        ->from('empresas_compartilhadas')
                        ->whereColumn('empresas_compartilhadas.empresa_id', 'empresas.id')
                        ->where('empresas_compartilhadas.solicitante_user_id', $userId)
                        ->where('empresas_compartilhadas.autorizado', 'S');
                });
            })
            ->find($id);
       
        if(empty($empresa)){
            session()->flash('message', 'Empresa não encontrada.');
            return redirect()->route('empresas.index');
        }

        $estados = $this->estadoModel->getListaEstados();
        $cidade = $empresa->cidade()->first();
        $uf_id = $empresa->cidade()->first()->estado()->first()->id;
        $cidades = $this->municipioModel->municipiosComEndPoint($uf_id);
        
        $cnaes = $this->cnaeModel->cnaesList($empresa->id);
        $atividades = $this->atividadeModel->atividadesList($empresa->id);
        
        $empresaCnaePrincipalId = $empresa->empresa_cnae_id;
        
        //filtro items lc conforme cnae
        $listaCnae = $this->cnaeModel->find($empresaCnaePrincipalId);
        if(!is_null($listaCnae)){
            $filtroLc = CnaeLc::where('cnae', $listaCnae->codigo_cnae)->get();
            $itemLcFiltro = [];
            foreach($filtroLc as $filter){
                $itemLcFiltro[] = $filter->item_lc;
            }
            $servicos = $this->listaServicoModel->getListaServicos($itemLcFiltro);
        }else{
            $servicos = [];
        }

        $regimeEspecialTributacaoList = $this->empresaModel->getRegimeEspecialTributacao();
        
        if(!empty($empresa->item_lc_id)){
            $nbs_list = $this->nbsModel->getListaNbs($empresa->item_lc_id);
        }else{
            $nbs_list = [];
        }

        $provedores = Empresa::getProvedorEmissao();
        $ambientes_emissao = Empresa::getAmbienteEmissao();

        $regimes_tributarios = Empresa::getRegimeTributario();//Situação perante Simples Nacional, preenche campo opSimpNac na NFSE em regTrib
        $situacao_simples_nacional = Empresa::getOpcaoSimplesNacional();//Regime de Apuração Tributária pelo Simples Nacional, campo regApTribSN em regTrib
        $tipos_regime_esp_trib_mun = Empresa::getTiposRegimeEspecialTributacaoMunicipio();

        return view('empresas.editar')->with([
            'estados' => $estados,
            'empresa' => $empresa,
            'uf_id' => $uf_id,
            'cidade' => $cidade,
            'cidades' => $cidades,
            'servicos' => $servicos,
            'atividades' => $atividades,
            'cnaes' => $cnaes,
            'reg_esp_trib' => $regimeEspecialTributacaoList,
            'nbs_list' => $nbs_list,
            'provedores' => $provedores,
            'ambientes_emissao' => $ambientes_emissao,
            'regimes_tributarios' => $regimes_tributarios,
            'situacao_simples_nacional' => $situacao_simples_nacional,
            'tipos_regime_esp_trib_mun' => $tipos_regime_esp_trib_mun,
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
        $userId = Auth::user()->id;
        $empresa = $this->empresaModel->find($id);


        switch($dados['cidade_id']):
            case '5208707'://goiania
                $dados['serie_nota'] = 1;
                break;
            case '5201405'://aparecidada de goiania
                $dados['serie_nota'] = 9;
                break;
            case '3301702'://duque de caxias
                $dados['serie_nota'] = 1;
                break;
            case '3543402'://Ribeirão Preto
                $dados['serie_nota'] = 1;
                break;
            default:
                $dados['serie_nota'] = 8;
                break;
        endswitch;

        $empresaCompartilhada = $this->empresaCompartilhadaModel->where('empresa_id', $id)
            ->where('autorizado', 'S')
            ->where('solicitante_user_id', $userId)->first();
        
        if( (!empty($empresa) && $empresa->user_id != $userId) || !is_null($empresaCompartilhada)){
            session()->flash('message', 'Você não pode editar essa Empresa.');
            return redirect()->route('empresas.edit', $id);
        }

        //blessed
        if (collect([56,78,79,86])->contains(auth()->id())) {
            $dados['plano_id'] = 1;
        }
       
        $empresa->update($dados);

        session()->flash('message', 'Registro Atualizado com Sucesso.');

        return redirect()->route('empresas.edit', $id);
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

    public function selecionarEmpresa(Request $request){
        $userId = Auth::user()->id;

        if($request->method() != 'POST'){
            $empresasList = $this->empresaModel
                ->empresasList(Auth::user()->id);
            
            //$qtdEmpresas = $this->empresaModel->where('user_id', Auth::user()->id)->count();
            $qtdEmpresas = $this->empresaModel
            ->where(function($query) use ($userId) {
                $query->where('user_id', $userId)
                ->orWhereExists(function($sub) use ($userId) {
                    $sub->select(DB::raw(1))
                        ->from('empresas_compartilhadas')
                        ->whereColumn('empresas_compartilhadas.empresa_id', 'empresas.id')
                        ->where('empresas_compartilhadas.solicitante_user_id', $userId)
                        ->where('empresas_compartilhadas.autorizado', 'S');
                }); 
            })
            ->count();
            
            if($qtdEmpresas == 1){
                //$empresaSelecionada = $this->empresaModel->where('user_id', Auth::user()->id)->first();
                $empresaSelecionada = $this->empresaModel
                ->where(function ($query) {
                    $query->where('user_id', Auth::user()->id)
                        ->orWhereExists(function ($sub) {
                            $sub->select(DB::raw(1))
                                ->from('empresas_compartilhadas')
                                ->whereColumn(
                                    'empresas_compartilhadas.empresa_id',
                                    'empresas.id'
                                )
                                ->where('empresas_compartilhadas.solicitante_user_id', Auth::user()->id)
                                ->where('empresas_compartilhadas.autorizado', 'S');
                        });
                })
                ->first();
                
                $license = $this->licenseModel
                    ->whereDate('validate', '>=', DB::raw('CURDATE()'))
                    ->where('empresa_id', $empresaSelecionada->id)->first();
            
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
                    session()->flash('danger', 'A Empresa '. $empresaSelecionada->razao_social .' não possui licença ativada.');
                    return redirect()->route('dashboard');
                }

                Session::put('empresa', $empresaSelecionada);
                Session::put('empresa_selecionada', $empresaSelecionada->id);
                                
                return redirect()->route('area-cliente');
            }elseif($qtdEmpresas == 0){
                session()->flash('danger', 'Não há empresa Cadastradas para seu Usuário.');
                return redirect()->route('dashboard');
            }

            
            return view('empresas.selecionar_empresa',[
                'empresasList' => $empresasList
            ]);
        }else{
            $empresaSelecionada = $this->empresaModel->find($request->get('empresa_id'));
            $license = $this->licenseModel
                ->whereDate('validate', '>=', DB::raw('CURDATE()'))
                ->where('empresa_id', $empresaSelecionada->id)->first();
            
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
                session()->flash('danger', 'A Empresa '. $empresaSelecionada->razao_social .' não possui licença ativada.');
                return redirect()->route('dashboard');
            }

            Session::put('empresa', $empresaSelecionada);
            Session::put('empresa_selecionada', $request->get('empresa_id'));
            return redirect()->route('area-cliente');
        }
    }

    public function sincronizarDadosIss($empresaId){
        try{
            $empresa = $this->empresaModel->find($empresaId);
            request()->session()->put('dados_empresa', $empresa);

            if(trim($empresa->inscricao_municipal) == '' || $empresa->inscricao_municipal == '00000'){
                session()->flash('danger', 'A Empresa '. $empresa->razao_social .' não possui inscrição municipal. A Inscrição Municipal deve ser informada.');
                return redirect()->route('empresas.edit', $empresa->id);
            }

            $certificadoValido = $this->certificadoModel
                ->where('empresa_id', $empresa->id)
                ->where('data_validade', '>=', now()->format('Y-m-d'))
                ->count();
            
            if($certificadoValido < 1){
                session()->flash('danger', 'A Empresa '. $empresa->razao_social .' não possui um certificado digital válido cadastrado. O Certificado Digital é necessário para a comunicação com o Sistema da Prefeitura.');
                return redirect()->route('empresas.edit', $empresa->id);
            }

            //$dados = $this->consultarDadosCadastrais($empresa->cpf_cnpj, $empresa->inscricao_municipal);
            //Log::info($dados);
            
            $consultarDadosCadastraisDTO = $this->nfse->consultarDadosCadastrais(
                $empresa->sigla_provedor,
                $empresaId, // 🔥 empresa dinâmica - referencia para buscar certificado digital,
                $empresa->cpf_cnpj, // 🔥 cnpj dinâmico
                $empresa->inscricao_municipal // 🔥 inscrição municipal dinâmica
            );
                        
            if(isset($dados['error'])){
                session()->flash('danger', $dados['message']);

                if(auth()->user()->is_admin){
                    return redirect()->route('admin.empresas.edit', $empresa->id);
                }else{
                    return redirect()->route('empresas.edit', $empresa->id);
                }
            }
            
            /*if(!isset($dados['Atividades']['Atividade']['CodigoTributacaoMunicipio'])){
                foreach ($dados['Atividades']['Atividade'] as $atividade) {
                    $empresaAtividade = new EmpresaAtividade();
                    $empresaAtividade->insere([
                        'empresa_id' => $empresa->id,
                        'codigo_atividade' => $atividade['CodigoTributacaoMunicipio'],
                        'descricao_atividade' => $atividade['DescricaoCodigoTributacaoMunicípio'],
                        'vigencia_inicial' => date('Y-m-d', strtotime($atividade['Vigencias']['Vigencia']['DataInicial'])),
                        'vigencia_final'   => (isset($atividade['Vigencias']['Vigencia']['DataFinal'])) 
                            ? date('Y-m-d', strtotime($atividade['Vigencias']['Vigencia']['DataFinal'])) : null,
                        'aliquota' => 0
                    ]);
                }
            }else{
                $empresaAtividade = new EmpresaAtividade();
                $empresaAtividade->insere([
                    'empresa_id' => $empresa->id,
                    'codigo_atividade' => $dados['Atividades']['Atividade']['CodigoTributacaoMunicipio'],
                    'descricao_atividade' => $dados['Atividades']['Atividade']['DescricaoCodigoTributacaoMunicípio'],
                    'vigencia_inicial' => date('Y-m-d', strtotime($dados['Atividades']['Atividade']['Vigencias']['Vigencia']['DataInicial'])),
                    'vigencia_final'   => (isset($dados['Atividades']['Atividade']['Vigencias']['Vigencia']['DataFinal'])) 
                        ? date('Y-m-d', strtotime($dados['Atividades']['Atividade']['Vigencias']['Vigencia']['DataFinal'])) : null,
                    'aliquota' => 0
                ]);
            }

            $baseCnaes = CnaeLc::pluck('descricao_cnae', 'cnae');

            $empresaCnae = new EmpresaCnae();
            if(is_array($dados['Cnaes']['CodigoCnae'])){
                foreach ($dados['Cnaes']['CodigoCnae'] as $key => $cnae) {
                    $descricao = $baseCnaes->get($cnae, 'CNAE não encontrado na base de dados do sistema.');
                    $empresaCnae->insere([
                        'empresa_id' => $empresa->id,
                        'codigo_cnae' => $cnae,
                        'descricao_cnae' => $descricao,
                        'principal' => ($key == 0) ? 1 : 2,
                    ]);
                }
            }else{
                $descricao = $baseCnaes->get($dados['Cnaes']['CodigoCnae'], 'CNAE não encontrado na base de dados do sistema.');
                $empresaCnae->insere([
                    'empresa_id' => $empresa->id,
                    'codigo_cnae' => $dados['Cnaes']['CodigoCnae'],
                    'descricao_cnae' => $descricao,
                    'principal' => 2,
                ]);
            }

            //MODIFICAR OS DADOS A SEREM SALVOS
            $empresa->is_mei = ($dados['OpcaoMei']['OptanteMei'] == 1) ? $dados['OpcaoMei']['OptanteMei'] : 0;
            $empresa->is_optante_simples_nac = ($dados['OpcaoSimplesNacional']['OptanteSimplesNacional'] == 1) ? 1 : 2;

            if($empresa->is_mei == 1){
                $empresa->regime_esp_tributacao = 5;
            }

            $empresa->dados_cadastrais = json_encode($dados);
            $empresa->save();*/

            /*DB::statement("
                UPDATE empresa_cnaes AS e
                JOIN cnae_lc AS c ON e.codigo_cnae = c.cnae
                SET e.descricao_cnae = c.descricao_cnae
                WHERE e.empresa_id = ?
            ", [$empresaId]);*/

            $empresa->dados_cadastrais = json_encode($consultarDadosCadastraisDTO);
            $empresa->save();
            
        }catch(\Exception $e){
            dd($e->getMessage());
        }

        if(Auth::user()->is_admin == 1){
            return redirect()->route('admin.empresas.edit', $empresa->id);
        }

        return redirect()->route('empresas.edit', $empresa->id);
    }

    public function listagemSolicitacaoCompartilhamentoEmpresa(){
        $userId = Auth::user()->id;
        
        $solicitacoes = $this->empresaCompartilhadaModel
            ->where('solicitante_user_id', $userId)
            //->where('autorizado', 'N')
            ->orderBy('created_at', 'desc')->paginate();
        
        return view('empresas.listagem_solicitacao_acesso_empresa')->with([
            'solicitacoes' => $solicitacoes
        ]);
    }

    public function novaSolicitacaoCompartilhamentoEmpresa(Request $request){
       return view('empresas.nova_solicitacao_acesso_empresa');
    }

    public function gravarSolicitacaoCompartilhamentoEmpresa(Request $request){
        $dados = $request->all();
        $userId = Auth::user()->id;
        $cnpj = preg_replace('/[^[:alnum:]_]/', '', $dados['cpf_cnpj']);
        $empresaSolicitada = $this->empresaModel
            ->where('user_id','<>', $userId)//não tem sentido solicitar acesso a minha propria empresa, apenas de terceiros
            ->where('cpf_cnpj', $cnpj)
            ->first();

        if(!is_null($empresaSolicitada)){
            $solicitacaoExiste = $this->empresaCompartilhadaModel->where('solicitante_user_id', $userId)
                ->where('empresa_id', $empresaSolicitada->id)
                //->where('autorizado', '')
                ->first();

            if(is_null($solicitacaoExiste)){
                $this->empresaCompartilhadaModel->create([
                    'empresa_id' => $empresaSolicitada->id,
                    'solicitante_user_id' => $userId,
                    'proprietario_user_id' => $empresaSolicitada->user_id,
                ]);

                session()->flash('sucess', 'Solicitação realizada com sucesso. Aguarde o usuário responsável pela empresa efetuar a liberação.');    
            }else{
                session()->flash('info', 'Você já solicitou acesso a esta empresa, verifique os status da liberação com o Usuário Responsável.');    
            }
        }else{
            session()->flash('danger', 'A Empresa solicitada do CNPJ informado não foi encontrada. Verifique se a empresa está cadastrada no sistema.');
        }

        return redirect()->route('empresas.listar-solicitacao-acesso-empresa');
    }

    public function listagemCompartilhamentoEmpresa(){
        $userId = Auth::user()->id;
        
        $solicitacoes = $this->empresaCompartilhadaModel
            ->where('proprietario_user_id', $userId)
            //->where('autorizado', 'N')
            ->orderBy('created_at', 'desc')->paginate();
        
        return view('empresas.listagem_acesso_empresa')->with([
            'solicitacoes' => $solicitacoes
        ]);
    }

    public function alteraSolicitacaoCompartilhamentoEmpresa(Request $request, $id){
        $dados = $request->all();
        $id = base64_decode($id);
        $userId = Auth::user()->id;
        
        $solicitacao = $this->empresaCompartilhadaModel
            ->where('proprietario_user_id', $userId)
            //->where('autorizado', 'N')
            ->where('id', $id)
            ->first();
        
        if($solicitacao->autorizado === 'S'){
            $solicitacao->autorizado = 'N';
            session()->flash('danger', 'Bloqueio Realizado com Sucesso. O usuário solicitante não pode mais acessar a Empresa.');    
        }else{
            session()->flash('success', 'Liberação de acesso realizado com Sucesso. Agora o usuário solicitante pode Gerenciar a Empresa.');    
            $solicitacao->autorizado = 'S';
        }
        $solicitacao->save();
        
        return redirect()->route('empresas.list-solicitar-acesso-empresa');
    }

    public function removerCompartilhamento(Request $request, $id){
        $id = base64_decode($id);
        $userId = Auth::user()->id;
        
        $solicitacao = $this->empresaCompartilhadaModel
            ->where('proprietario_user_id', $userId)
            //->where('autorizado', 'N')
            ->where('id', $id)
            ->first();
        $solicitacao->delete();
        session()->flash('success', 'Solicitação de Compartilhamento de Empresa removido com sucesso.');    
        return redirect()->route('empresas.list-solicitar-acesso-empresa');
    }
}
