<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresaTributacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresa_tributacoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')
                ->constrained('empresas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('cst_ibs_cbs_id')
                ->constrained('cst_ibs_cbs')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('classificacao_tributaria_id')
                ->constrained('classificacoes_tributarias')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->string('c_ind_op', 1)->nullable();
            $table->string('op_simp_nac', 1)->nullable();
            $table->string('reg_ap_ibs_cbs_sn', 1)->nullable();
            $table->string('c_atv_sn', 1)->nullable();

            $table->date('vigencia_inicio')->nullable();
            $table->date('vigencia_fim')->nullable();

            $table->boolean('ativo')->default(true);

            $table->timestamps();

            $table->index('empresa_id');
            $table->index('cst_ibs_cbs_id');
            $table->index('classificacao_tributaria_id');

            $table->index([
                'empresa_id',
                'ativo',
                'vigencia_inicio',
                'vigencia_fim'
            ], 'emp_vig_ativo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresa_tributacoes');
    }
}
