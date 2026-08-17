<?php

namespace App\Jobs;

use App\Mail\AlertLicenses;
use App\Models\License;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailLicensesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
        
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //$rs = License::whereDate('validate', '<=', Carbon::now()->addDays(15))->get();
        //Log::info($rs->count());
        

        $data = [];
        $data['subject'] = 'Licenças Vencendo em Dias'; 
        $data['responsavel'] = 'josueprg@gmail.com';

        $rs = DB::table('licenses')
            ->select('empresas.razao_social', 'empresas.cpf_cnpj', 'licenses.validate')
            ->join('empresas','empresas.id','=','licenses.empresa_id')
            ->whereDate('validate', '>=', Carbon::now())
            ->whereDate('validate', '<=', Carbon::now()->addDays(15))
            ->orderBy('validate', 'ASC')
            ->get();
        
        $data['registros'] = $rs;

        if(sizeof($data['registros']) > 0){
            Mail::to($data['responsavel'])
                    ->send(new AlertLicenses($data));
        }
    }
}
