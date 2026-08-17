<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCnaeAtividadeMunicipioEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('empresas', function (Blueprint $table) {
            //$table->unsignedBigInteger('empresa_cnae_id');
            /*$table->foreign('empresa_cnae_id')
            ->references('id')
            ->on('empresa_cnaes');*/

            //$table->unsignedBigInteger('empresa_atividades_id');
            /*$table->foreign('empresa_atividades_id')
            ->references('id')
            ->on('empresa_atividades');*/
        });
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
