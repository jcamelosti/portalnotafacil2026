<?php

namespace App\Console\Commands;

use App\Models\Fatura;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteBillsExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete_bills_expired:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Faturas Expiradas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {       
        $faturas = Fatura::where('fatura_status_id', 1)
            ->whereRaw('HOUR(TIMEDIFF(NOW(), created_at)) > 1')
            ->get();

        foreach($faturas as $fatura){
            if($fatura->items()->count() > 0){
                $fatura->items()->delete();
            }
            $fatura->delete();
        }
    }
}
