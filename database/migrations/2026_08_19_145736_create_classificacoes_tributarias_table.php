<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassificacoesTributariasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classificacoes_tributarias', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 6);
            $table->string('descricao');

            $table->foreignId('cst_id')
                ->constrained('cst_ibs_cbs')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->date('vigencia_inicio')->nullable();
            $table->date('vigencia_fim')->nullable();

            $table->boolean('ativo')->default(true);

            $table->timestamps();

            $table->unique(['cst_id', 'codigo']);

            $table->index(['ativo', 'vigencia_inicio', 'vigencia_fim'], 'class_trib_status_vig');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classificacoes_tributarias');
    }
}
