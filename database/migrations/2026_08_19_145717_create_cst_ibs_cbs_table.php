<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCstIbsCbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cst_ibs_cbs', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 3);
            $table->string('descricao');

            $table->date('vigencia_inicio')->nullable();
            $table->date('vigencia_fim')->nullable();

            $table->boolean('ativo')->default(true);

            $table->timestamps();

            $table->unique('codigo');

            $table->index(['ativo', 'vigencia_inicio', 'vigencia_fim']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cst_ibs_cbs');
    }
}
