<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanoVariacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::create('plano_variacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plano_id');
            $table->foreign('plano_id')->references('id')->on('planos');
            $table->text('descricao');
            $table->decimal('valor', 15,2);
            $table->integer('fator_vigencia')->default(30);
            $table->timestamps();
            $table->softDeletes();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plano_variacoes');
    }
}
