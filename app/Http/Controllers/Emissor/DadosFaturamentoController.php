<?php

namespace App\Http\Controllers\Emissor;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Municipio;
use App\Models\Uf;
use App\Utilitarios\Utilitarios;
use Illuminate\Http\Request;

class DadosFaturamentoController extends Controller
{
    private $estadoModel;
    private $municipioModel;
    
    public function __construct(Uf $estadoModel, Municipio $municipioModel)
    {
       //throw new \Exception('Not implemented');
        $this->estadoModel = $estadoModel;
        $this->municipioModel = $municipioModel;
    }

    public function create(Request $request){
        $cliente = new Cliente();
        $estados = $this->estadoModel->getListaEstados();
        $cidades = $this->municipioModel->municipios();
        $uf_id = 9;

        //return view('dados-faturamento.create', compact('cliente'));
        return view('dados-faturamento.create')->with([
            'cliente' => $cliente,
            'uf_id' => !empty($estado) ? $estado->id : $uf_id,
            'estados' => $estados,
            'cidades' => $cidades,
            //'estado' => $estado
        ]);
    }

    public function store(Request $request){
        $dados = $request->all();
        $dados['user_id'] = auth()->user()->id;
        
        $cliente = Cliente::create($dados);
        
        Utilitarios::sendMessage(auth()->user()->name . " inseriu os dados de faturamento.");

        return redirect()->route('dashboard');
    }

    public function edit(Request $request){
        $cliente = Cliente::where('user_id', auth()->user()->id)->first();
        
        $estados = $this->estadoModel->getListaEstados();
        $cidades = $this->municipioModel->municipios();
        $uf_id = 9;

        //return view('dados-faturamento.create', compact('cliente'));
        return view('dados-faturamento.edit')->with([
            'cliente' => $cliente,
            'uf_id' => !empty($estado) ? $estado->id : $uf_id,
            'estados' => $estados,
            'cidades' => $cidades,
            //'estado' => $estado
        ]);
    }

    public function update(Request $request){
        $dados = $request->all();
        $cliente = Cliente::where('user_id', auth()->user()->id)->first();
        
        $cliente->update($dados);

        Utilitarios::sendMessage(auth()->user->name . " atualizou os dados de faturamento.");

        return redirect()->route('dashboard');
    }    
}
