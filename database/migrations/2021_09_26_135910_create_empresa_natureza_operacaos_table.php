<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresaNaturezaOperacaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresa_natureza_operacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
//            $table->foreign('empresa_id')
//                ->references('id')
//                ->on('empresas');

            $table->string('codigo_nat_op');
            $table->string('descricao_nat_op');
            $table->date('vigencia_inicial');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresa_natureza_operacoes');
    }
}
