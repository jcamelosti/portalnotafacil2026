<?php

namespace App\Http\Middleware;

use App\Models\License;
use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ValidateLicenseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $empresaId = Session::get('empresa_selecionada');

        if( ($request->segment(2) == 'emissor' && $request->segment(4) == 'criar') || $request->segment(3) == 'servicos-prestados'){
            $license = License::whereDate('validate', '>=', DB::raw('CURDATE()'))
            ->where('empresa_id', $empresaId)->first();
        
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
                session()->flash('danger', 'Está Empresa não possui licença ativada.');
                return redirect('c/area-cliente');
            }
        }

        return $next($request);
    }
}
