<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\NotaEmitida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaController extends Controller
{
    private $nfseModel;
    private $empresaModel;
    
    public function __construct(NotaEmitida $nfseModel, Empresa $empresaModel){
        $this->nfseModel = $nfseModel;
        $this->empresaModel = $empresaModel;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $empresasList = $this->empresaModel
            ->empresasList();
     
        $campos = request()->all();

        $notas = $this->nfseModel
            //->whereMonth('created_at', '=', date('m'))
            ->where(function($query) use($campos) {
                if(isset($campos['empresa_id']) && $campos['empresa_id'] != '0'){
                    $query->where('empresa_id', $campos['empresa_id']);
                }
            })
            ->orderBy('id', 'DESC')
            ->paginate(10);
            
        return view('admin.notas-emitidas.index', [
            'notas' => $notas,
            'empresasList' => $empresasList,
            'pesquisa' => $campos
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
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

    public function errosUsuarios(){
        DB::table('internal_logs')
            ->where('created_at', '<', DB::raw('NOW() - INTERVAL 1 HOUR'))
            ->delete();

        $erros = DB::table('internal_logs as il')
            ->join('empresas as e', 'e.id', '=', 'il.empresa_id')
            ->select(
                'e.cpf_cnpj',
                'e.razao_social',
                'il.description',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('e.cpf_cnpj', 'e.razao_social', 'il.description')
            ->orderByDesc('total')
            ->get();
        
        return view('admin.notas-emitidas.erros', [
            'erros' => $erros,
        ]);
    }
}
