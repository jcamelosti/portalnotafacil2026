<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\NotaEmitida;
use App\Services\EmissorNotaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaController extends Controller
{
    private $empresaModel;
    private $nfseModel;
    private $emissorService;

    public function __construct(Empresa $empresaModel, NotaEmitida $nfseModel, EmissorNotaService $emissorService){
        $this->empresaModel = $empresaModel;
        $this->nfseModel = $nfseModel;
        $this->emissorService = $emissorService;
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
            ->paginate(20);
            
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

    public function obterUrlNfse(NotaEmitida $nota)
    {
        if (!$nota) {
            return response()->json(['error' => 'Nota não encontrada'], 404);
        }

        $consulta = $this->emissorService->consultarUrlNota($nota->empresa, $nota->num_nfse);

        if($consulta['error']){
            session()->flash('danger', $consulta['message']);
            return redirect()->back();
        }
        
        return $consulta;
    }

    public function validacoes(){
        DB::table('validacoes_xml')
            ->where('created_at', '<', DB::raw('NOW() - INTERVAL 1 HOUR'))
            ->delete();

        $registros = DB::table('validacoes_xml as il')
            ->join('empresas as e', 'e.id', '=', 'il.empresa_id')
            ->select(
                'e.cpf_cnpj',
                'e.razao_social',
                'il.xml',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('e.cpf_cnpj', 'e.razao_social', 'il.xml')
            ->orderByDesc('total')
            ->get();
        
        $resultados = [];

    foreach ($registros as $registro) {

            $xml = simplexml_load_string($registro->xml);

            if ($xml === false) {
                $resultados[] = [
                    'cpf_cnpj' => $registro->cpf_cnpj,
                    'razao_social' => $registro->razao_social,
                    'codigo' => null,
                    'mensagem' => 'Não foi possível interpretar o XML.',
                    'sucesso' => false,
                ];

                continue;
            }

            $mensagens = $xml->xpath(
                '//*[local-name()="MensagemRetorno"]'
            );

            foreach ($mensagens as $item) {

                $codigoNode = $item->xpath('./*[local-name()="Codigo"]');
                $mensagemNode = $item->xpath('./*[local-name()="Mensagem"]');

                $codigo = isset($codigoNode[0])
                    ? (string) $codigoNode[0]
                    : '';

                $mensagem = isset($mensagemNode[0])
                    ? (string) $mensagemNode[0]
                    : 'Mensagem não informada.';

                $resultados[] = [
                    'cpf_cnpj' => $registro->cpf_cnpj,
                    'razao_social' => $registro->razao_social,
                    'codigo' => $codigo,
                    'mensagem' => $mensagem,
                    'sucesso' => $codigo === 'S000',
                ];
            }
        }
        
        return view('admin.notas-emitidas.validacoes', [
            'resultados' => $resultados,
        ]);
    }
}
