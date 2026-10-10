<?php

namespace App\Http\Controllers\ServicosTomados;

use App\Http\Controllers\Controller;
use App\Models\DocumentoFiscalRecebido;
use App\Models\Empresa;
use App\Models\License;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class NotaTomadasController extends Controller
{
    private $empresaModel;
    private $licenseModel;

     public function __construct(
        Empresa $empresaModel,
        License $licenseModel

    )
    {
        $this->empresaModel = $empresaModel;
        $this->licenseModel = $licenseModel;
    }

    public function selecionarEmpresa(Request $request){
        $userId = Auth::user()->id;

        Session::forget('empresa');
        Session::forget('empresa_selecionada');

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
                                
                return redirect()->route('servicos-tomados.index');
            }elseif($qtdEmpresas == 0){
                session()->flash('danger', 'Não há empresa Cadastradas para seu Usuário.');
                return redirect()->route('dashboard');
            }

            return view('servicos-tomados.selecionar_empresa',[
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
            return redirect()->route('servicos-tomados.index');
        }
    }

    public function index(){
        $dataLiberado = '2026-10-10';

        if(is_null(session()->get('empresa_selecionada'))){
            session()->flash('info', 'É necessário selecionar uma Empresa');
            return redirect()->route('servicos-tomados.selecionar-empresa');
        }

        $empresa = Empresa::where('id', session()->get('empresa_selecionada'))->first();

        //data atual menor ou igual ao limite dataLiberado
        if (Carbon::today()->lte(Carbon::parse($dataLiberado))) {
            //
        }else{
            if($empresa->plano->consulta_dfe == 0){
                session()->flash('info', 'Seu plano atual não possui acesso ao Recurso de Consultar de Notas(DF-e)');
                return redirect()->back();
            }
        }

        $documentos = DocumentoFiscalRecebido::query()
            ->where('empresa_id', $empresa->id)
            ->where('tipo_documento', 'NFSE')
            ->orderByDesc('nsu')
            ->paginate(20);

        return view('servicos-tomados.index')->with([
            'documentos' => $documentos,
        ]);
    }

    public function visualizarXmlNota(DocumentoFiscalRecebido $nota){  
        $empresa = $this->empresaModel->find($nota->empresa->id);

        if($nota->empresa_id != Session::get('empresa_selecionada')){
            session()->flash('danger', 'Acesso negado.');
            return redirect()->route('servicos-tomados.index');
        }

        $nomeArquivo = 'NFSE-' . $nota['dados']['header']['chave'] . '.xml';
        Storage::disk('local')->put('public/' . $empresa->id . '/' . $nomeArquivo, $nota->xml);
        $caminhoDownload = storage_path() . '/app/public/' . $empresa->id . '/' . $nomeArquivo;
        header('Content-disposition: attachment; filename="' . $nomeArquivo . '"');
        header('Content-type: "text/xml"; charset="utf8"');
        readfile($caminhoDownload);
    }
}
