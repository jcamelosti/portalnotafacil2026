<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresaNbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresa_nbs', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('empresa_id')->index();
            $table->foreign('empresa_id')
                ->references('id')
                ->on('empresas');

            $table->unsignedBigInteger('correlaca_trib_id')->index();
            $table->foreign('correlaca_trib_id')
                ->references('id')
                ->on('correlacao_trib_mun_trib_nac');
            
            $table->string('codigo', 50);
            $table->string('descricao', 500);
            
            $table->timestamps();

            $table->unique(['empresa_id', 'correlaca_trib_id', 'codigo']); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresa_nbs');
    }
}
