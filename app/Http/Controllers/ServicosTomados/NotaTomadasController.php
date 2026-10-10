<?php

namespace App\Http\Controllers\ServicosTomados;

use App\Http\Controllers\Controller;
use App\Models\DocumentoFiscalRecebido;
use App\Models\Empresa;
use Illuminate\Http\Request;

class NotaTomadasController extends Controller
{
    public function index(){
        $empresa = Empresa::where('id', 1)->first();
        $documentos = DocumentoFiscalRecebido::query()
            ->where('empresa_id', $empresa->id)
            ->where('tipo_documento', 'NFSE')
            ->orderByDesc('nsu')
            ->paginate(20);

        dd($documentos);
    }
}
