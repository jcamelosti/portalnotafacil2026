<?php

namespace App\Http\Controllers\Empresas;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Fatura;
use Illuminate\Http\Request;

class FaturaController extends Controller
{
    private $faturaModel;
    private $empresaModel;

    public function __construct(
        Fatura $faturaModel,
        Empresa $empresaModel
    )
    {
        $this->faturaModel = $faturaModel;
        $this->empresaModel = $empresaModel;
    }

    public function index()
    {
        $faturas = $this->faturaModel
            ->where('user_id', auth()->user()->id)
            ->orderBy('id', 'DESC')
            ->paginate(20);

        $empresasList = $this->empresaModel
            ->empresasList(auth()->user()->id);
        
        return view('faturas.index', compact('faturas', 'empresasList'));
    }

    public function show($id){
        $fatura = $this->faturaModel->find($id);
        return view('faturas.show', compact('fatura'));
    }
}
