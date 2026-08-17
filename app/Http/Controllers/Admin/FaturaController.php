<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fatura;
use App\Models\Servico;
use App\Utilitarios\Utilitarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FaturaController extends Controller
{
    private $faturaModel;

    public function __construct(
        Fatura $faturaModel
    )
    {
        $this->faturaModel = $faturaModel;
    }

    public function index(){
        //$this->deleteExpiredBills();

        $faturas = $this->faturaModel
            //->whereRaw(DB::raw("DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 2 DAY)"))
            //->whereRaw(DB::raw("DATE(created_at) < CURRENT_DATE"))
            //->whereDate('created_at', '=', DB::raw('CURDATE()'))
            ->orderBy('id', 'DESC')
            ->paginate(20);

        $totais = $this->totais();

        $proximoDiaUtil = Utilitarios::proximoDiaUtil(date('Y-m-d'), 'd/m/y');

        return view('admin.faturas.index', compact('faturas', 'totais', 'proximoDiaUtil'));
    }

    public function show($id){
        $fatura = $this->faturaModel->find($id);
        return view('admin.faturas.show', compact('fatura'));
    }

    protected function totais(){
        $finalDeSemana = date('N', strtotime(date('Y-m-d'))) >= 5 ? true : false;
        $diaAtualDaSemana = date('N', strtotime(date('Y-m-d')));
        $fatorProximoDiaUtil = (8 - $diaAtualDaSemana);
        $totais = [];

        //total recebido no mês
        $totalMes = $this->faturaModel
            ->selectRaw("SUM(total) as total_mes")
            ->whereMonth('created_at', '=', date('m'))
            ->where('fatura_status_id', 3)
            ->first();
        $totalMes = $totalMes['total_mes'];

        if(!$finalDeSemana){//caso não seja final de semana
            //total recebido no dia
            $totalDia = $this->faturaModel
                ->selectRaw("SUM(total) as total_dia")
                ->whereDate('created_at', '=', DB::raw('CURDATE()'))
                ->where('fatura_status_id', 3)
                ->first();

            $totalDia = $totalDia['total_dia'];

            if($diaAtualDaSemana > 1 ){
                //total recebido ontem
                $totalDiaAnterior = $this->faturaModel
                    ->selectRaw("SUM(total) as total_dia_anterior")
                    ->whereRaw(DB::raw("DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)"))
                    ->where('fatura_status_id', 3)
                    ->first();

                $totalDiaAnterior = $totalDiaAnterior['total_dia_anterior'];
            }else{
                //total no final de semana
                $totalDiaAnterior = $this->faturaModel
                    ->selectRaw("SUM(total) as total_dia_anterior")
                    ->whereRaw(DB::raw("DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 3 DAY)"))
                    ->whereRaw(DB::raw("DATE(created_at) < CURRENT_DATE"))
                    ->where('fatura_status_id', 3)
                    ->first();

                $totalDiaAnterior = $totalDiaAnterior['total_dia_anterior'];
            }
        }else{
            //total recebido no final de semana    
            $proximoDiaUtil = Utilitarios::proximoDiaUtil(date('Y-m-d'), 'Y-m-d');

            //total recebido no dia
            $totalDia = $this->faturaModel
                ->selectRaw("SUM(total) as total_dia")
                ->whereDate('created_at', '=', DB::raw('CURDATE()'))
                ->where('fatura_status_id', 3)
                ->first();

            $totalDia = $totalDia['total_dia'];
                       
            //total recebido no dia anterior a hoje
            $totalDiaAnterior = $this->faturaModel
                ->selectRaw("SUM(total) as total_dia_anterior")
                //->whereRaw(DB::raw("DATE(created_at) BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND '$proximoDiaUtil'"))
                ->where(function($query) use ($diaAtualDaSemana, $proximoDiaUtil, $fatorProximoDiaUtil) {
                    if($diaAtualDaSemana == 5){
                        $query
                            ->whereRaw(DB::raw("DATE(created_at) = CURRENT_DATE"))
                            ->whereRaw(DB::raw("DATE(created_at) <= '$proximoDiaUtil'"));
                    }else{
                        //se é 2 dias faltantes para atingir proximo dia util então -1 na data atual
                        //se é 1 dias faltantes para atingir proximo dia util então -2 na data atual
                        if($fatorProximoDiaUtil == 2){
                            $query
                                ->whereRaw(DB::raw("DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)"));
                        }else{
                            $query
                                ->whereRaw(DB::raw("DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 2 DAY)"));
                        }
                        $query->whereRaw(DB::raw("DATE(created_at) <= '$proximoDiaUtil'"));
                    }
                })
                ->where('fatura_status_id', 3)
                ->first();
            $totalDiaAnterior = $totalDiaAnterior['total_dia_anterior'];
        }

        $totais['total_mes'] = ($totalMes - ( ($totalMes * 1) / 100) );
        $totais['total_dia'] = ($totalDia - ( ($totalDia * 1) / 100) );//recebido hoje
        /*$totais['total_credito_conta_amanha'] = ($totalDia - ( ($totalDia * 1) / 100) );//recebido hoje cai na conta amanhã
        $totais['total_credito_conta_hoje'] = ($totalDiaAnterior - ( ($totalDiaAnterior * 1) / 100) );//recebido hoje cai na conta amanhã*/

        return $totais;
    }

    protected function deleteExpiredBills(){
        $faturas = $this->faturaModel->where('fatura_status_id', 1)
        ->whereRaw('HOUR(TIMEDIFF(NOW(), created_at)) > 1')
        ->get();

        foreach($faturas as $fatura){
            if($fatura->items()->count() > 0){
                $fatura->items()->delete();
            }
            $fatura->delete();
        }
    }

    public function gerarServico(Fatura $fatura){
       /* */

        $descricaoServico = '';

        $hasItem = $fatura->items()->count();

        if($hasItem >0){
            $plano = $fatura->items()
                            ->first()
                            ->plano()
                            ->first();

            $varPlano = $fatura->items()
                ->first()
                ->variacaoPlano()
                ->first();

            $descricaoServico = 'Ref. Licença de Uso Sistema Emissor Notas Fiscais - Período Liberação: ' . $varPlano->descricao;
        }else{
            $descricaoServico = 'Ref. Licença de Uso Sistema Emissor Notas Fiscais';
        }

        try{
            Servico::create([
                'empresa_id' => 1, //id da minha empresa 24685881000190
                'fatura_id' => $fatura->id,
                'empresa_cliente_id' => $fatura->empresa_id,
                'local_prestacao' => 5201108, //Anápolis por Default
                'cod_trib_nacional_id' => 111,
                'nbs_id' => 916,
                'valor_servico' => $varPlano->valor,
                'descricao' => $descricaoServico
            ]);
        }catch(\Exception $e){
            //
        }
        return redirect()->route('admin.faturas.index');
    }
}
