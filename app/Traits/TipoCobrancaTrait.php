<?php
namespace App\Traits;

use App\Models\CreditoUser;
use App\Models\PlanoVariacao;
use Illuminate\Support\Facades\Auth;

trait TipoCobrancaTrait
{
    public function checaSaldoEmissaoPorNota(){
        $empresaSessao = request()->session()->get('empresa_selecionada');
        $credito = CreditoUser::where('user_id', Auth::user()->id)->first();
        $valorPlano = PlanoVariacao::where('plano_id', $empresaSessao->plano_id)->first();

        if(getenv("TIPO_COBRANCA_POR_NOTA") == 1){
            //se o crédito que tenho é menor que o valor minímo da emissao - negar
            if($credito->credito <= $valorPlano->valor){
                return false;
            }
        }
        return true;
    }
}