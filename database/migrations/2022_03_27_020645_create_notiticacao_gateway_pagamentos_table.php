<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotiticacaoGatewayPagamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::create('notificacao_gateway_pagamentos', function (Blueprint $table) {
            $table->increments('id');
            $table->json('conteudo_notificacao');
            $table->char('processado',1)->default('N');
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
        Schema::dropIfExists('notiticacao_gateway_pagamentos');
    }
}
