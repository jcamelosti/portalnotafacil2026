<?php

namespace App\Console\Commands;

use App\Models\NotaEmitida;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemoverNotasMais4Meses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remover-notas-antigas:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove notas com mais de 4 meses da base local';

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
        /*NotaEmitida::
            whereDate('data_emissao_nfse', '<', Carbon::now()->subMonth(3))
            ->delete();*/

        return 0;
    }
}
