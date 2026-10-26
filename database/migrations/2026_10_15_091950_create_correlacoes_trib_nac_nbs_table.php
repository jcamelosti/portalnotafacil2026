<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorrelacoesTribNacNbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correlacoes_trib_nac_nbs', function (Blueprint $table) {
            $table->id();

            // Código e descrição da tributação nacional
            $table->string('c_trib_nac', 6)->index();
            $table->text('x_trib_nac');

            // Código e descrição NBS
            $table->string('c_nbs', 9)->index();
            $table->text('x_nbs');

            // Classificação tributária IBS/CBS
            $table->string('c_class_trib', 6)->index();
            $table->text('x_class_trib');

            // CST IBS/CBS
            $table->string('cst', 3)->index();
            $table->string('x_st', 100);

            // Indicador da operação
            $table->string('c_ind_op', 6)->index();
            $table->string('x_ind_op', 150);

            // Indicadores complementares
            $table->string('ps_onerosa', 2)->nullable();
            $table->string('adq_exterior', 2)->nullable();
            $table->string('local_incidencia_ibs', 100)->nullable();

            $table->timestamps();

            // Índice composto para consultas de correlação
            $table->index(
                ['c_trib_nac', 'c_nbs', 'c_class_trib'],
                'idx_correlacao_trib_nbs_class'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('correlacoes_trib_nac_nbs');
    }
}
