<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaturaItensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::create('fatura_itens', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('fatura_id')->index()->nullable();
            $table->foreign('fatura_id')
                ->references('id')
                ->on('faturas');

            $table->unsignedBigInteger('plano_id')->index()->nullable();
            $table->foreign('plano_id')
                ->references('id')
                ->on('planos');

            $table->unsignedBigInteger('variacao_plano_id')->index()->nullable();
            $table->foreign('variacao_plano_id')
                ->references('id')
                ->on('plano_variacoes');

            $table->decimal('valor', 15,2);
            $table->smallInteger('qtd')->default(1);

            $table->timestamps();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fatura_itens');
    }
}
