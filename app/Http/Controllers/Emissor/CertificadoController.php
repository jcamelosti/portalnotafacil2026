<?php

namespace App\Http\Controllers\Emissor;

use App\Http\Controllers\Controller;
use App\Models\Certificado;
use App\Models\Empresa;
use App\Utilitarios\CertificadoDigital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use NFePHP\Common\Certificado as CommonCertificado;

class CertificadoController extends Controller
{
    private $certificadoModel;
    private $empresaModel;

    //Injetado TypeHint
    public function __construct(Certificado $certificadoModel, Empresa $empresaModel)
    {
        $this->certificadoModel = $certificadoModel;
        $this->empresaModel = $empresaModel;
    }

    public function index(Request $request)
    {
        $empresasList = $this->empresaModel
            ->empresasList(Auth::user()->id);
     
        $campos = $request->all();

        $certificados = $this->certificadoModel
            ->where(function($query) use($campos) {
                if(isset($campos['empresa_id']) && $campos['empresa_id'] != '0'){
                    $query->where('id', $campos['empresa_id']);
                }
            })
            ->where('user_id', Auth::user()->id)
            ->orderBy('razao_social', 'ASC')
            ->paginate();
    
        return view('certificados.index')->with([
            'certificados' => $certificados,
            'empresasList' => $empresasList
        ]);
    }

    public function create()
    {
        $empresas = ['' => 'Selecione a Empresa'] + $this->empresaModel
                ->where('user_id', Auth::user()->id)
                ->orderBy('razao_social', 'asc')
                ->pluck('razao_social', 'id')
                ->all();

        return view('certificados.criar', compact('empresas'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $certificado = $this->certificadoModel
            //->where('user_id', Auth::user()->id)
            ->where('empresa_id', $data['empresa_id'])
            ->first();

        if($certificado){
            session()->flash('danger', 'Já existe um certificado cadastrado para esta empresa. Altere o certificado existente ou exclua-o para cadastrar um novo.');
            return redirect()->route('empresas-certificados.index');   
        }

        $rules = array(
            'empresa_id' => 'required',
            'password' => 'required|between:4,100|confirmed'
        );
        $messages = array(
            'empresa_id' => 'Informe a Empresa',
            'password.required' => 'É necessário informar a Senha do Certificado.',
            'password.between' => 'A senha deve possui de 4 a 100 digitos.',
            'password.confirmed' => 'As Senhas não coincidem. Redigite-as',
        );

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return redirect()->route('empresas-certificados.create')->withErrors($validator);
        }else{
            $empresa = $this->empresaModel->find($request->get('empresa_id'));

            if($request->hasFile('certificado')){
                $certificado = $request->file('certificado');
                $nameArquivo = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj).'.'.$certificado->getClientOriginalExtension();
                $destinationPath = storage_path('/certificados/');
                $certificado->move($destinationPath, $nameArquivo);
                $caminhoCertificado = storage_path('/certificados/').$nameArquivo;
                $dadosCertificado = CertificadoDigital::lerDadosCertificado($caminhoCertificado, $request->get('password'));

                if(isset($dadosCertificado['data_validade'])){
                    $this->certificadoModel->create([
                        'user_id' => Auth::user()->id,
                        'empresa_id' => $request->get('empresa_id'),
                        'razao_social' =>  $dadosCertificado['razao_social'],
                        'senha' => base64_encode($request->get('password')),
                        'arquivo' => $nameArquivo,
                        'data_validade' => $dadosCertificado['data_validade']
                    ]);

                    $this->gerarPem($empresa, $caminhoCertificado, $request->get('password'));

                    session()->flash('message', 'Cadastro de Certificado realizado com sucesso.');
                    return redirect()->route('empresas-certificados.index');
                }else{
                    return redirect()->route('empresas-certificados.create')->withErrors($dadosCertificado);
                }
            }
        }
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $certificado = $this->certificadoModel
            ->where('user_id', Auth::user()->id)
            ->find($id);

        $empresas = ['' => 'Selecione a Empresa'] + $this->empresaModel
            ->where('user_id', Auth::user()->id)
            ->orderBy('razao_social', 'asc')
            ->pluck('razao_social', 'id')
            ->all();
       
        return view('certificados.editar', compact('certificado', 'empresas'));
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
        $data = $request->all();
        $certificadoUpd = $this->certificadoModel->find($id);

        if($certificadoUpd->user_id != Auth::user()->id){
            session()->flash('danger', 'Não foi possível atualizar o certificado.');
            return redirect()->route('empresas-certificados.edit', $certificadoUpd->id);
        }

        try{
            $rules = array(
                'empresa_id' => 'required',
                'password' => 'required|between:4,100|confirmed'
            );
            $messages = array(
                'empresa_id' => 'Informe a Empresa',
                'password.required' => 'É necessário informar a Senha do Certificado.',
                'password.between' => 'A senha deve possui de 4 a 100 digitos.',
                'password.confirmed' => 'As Senhas não coincidem. Redigite-as',
            );

            $validator = Validator::make($data, $rules, $messages);
            
            if ($validator->fails()) {
                session()->flash('danger', 'Não foi possível atualizar o certificado.');
                return redirect()->route('empresas-certificados.edit', $certificadoUpd->id)->withErrors($validator);
            }else{
                $empresa = $this->empresaModel->find($request->get('empresa_id'));

                if($request->hasFile('certificado')){
                    $certificado = $request->file('certificado');
                    $nameArquivo = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj).'.'.$certificado->getClientOriginalExtension();
                    $destinationPath = storage_path('certificados/');
                    $certificado->move($destinationPath, $nameArquivo);
                    $caminhoCertificado = storage_path('certificados/').$nameArquivo;
                 
                    $dadosCertificado = CertificadoDigital::lerDadosCertificado($caminhoCertificado, $request->get('password'));
                    
                    if(isset($dadosCertificado['data_validade'])){
                        $certificadoUpd->update([
                            'razao_social' =>  $dadosCertificado['razao_social'],
                            'senha' => base64_encode($request->get('password')),
                            'arquivo' => $nameArquivo,
                            'data_validade' => $dadosCertificado['data_validade']
                        ]);
                        
                        $this->gerarPem($empresa, $caminhoCertificado, $request->get('password'));
                        
                        session()->flash('message', 'Registro Atualizado com Sucesso.');
                        return redirect()->route('empresas-certificados.edit', $certificadoUpd->id)->withErrors($dadosCertificado);
                    }else{
                        
                        session()->flash('danger', 'Não foi possível atualizar o certificado.');
                        return redirect()->route('empresas-certificados.edit', $certificadoUpd->id)->withErrors($dadosCertificado);
                    }
                }
            }
        }catch(\Exception $e){
            dd($e->getMessage());
            session()->flash('danger', 'Não foi possível atualizar o certificado.');
            return redirect()->route('empresas-certificados.edit', $certificadoUpd->id);
        }
    }

    protected function gerarPem($empresa, $caminhoCertificado, $senhaCertificado){
        try{
            $oCert = new CommonCertificado();
                    
            if(getenv("AMBIENTE_PRODUCAO") == 0){
                $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_LOCAL");
            }else{
                $oCert->pathCerts = getenv("CAMINHO_CERTIFICADO_PROD");
            }

            $oCert->cnpj = $empresa->cpf_cnpj;
            $oCert->loadPfxFile(
                $caminhoCertificado,
                $senhaCertificado
            );

            $oCert->signXML('<root><item>1</item></root>', 'root');
        }catch(\Exception $e){
            dd($e->getMessage());
        }
    }
}
