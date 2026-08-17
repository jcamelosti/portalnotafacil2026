<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlanoIdEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::table('empresas', function (Blueprint $table) {
            $table->unsignedBigInteger('plano_id')->index()->nullable();
            $table->foreign('plano_id')->references('id')
                ->on('planos')->default(1);
         });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
