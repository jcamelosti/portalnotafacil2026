<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProtocolosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('protocolos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id')->index()->nullable();
            $table->foreign('empresa_id')
                ->references('id')
                ->on('empresas');

            $table->unsignedBigInteger('tomador_id')->index()->nullable();
            $table->foreign('tomador_id')
                ->references('id')
                ->on('tomadores');

            $table->bigInteger('num_nfse');
            $table->text('protocolo');
            $table->text('descricao_servico');    
            $table->decimal('valor_liquido', 15, 2);        
            $table->decimal('valor_total', 15, 2);
            $table->text('mensagem');    
            
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
        Schema::dropIfExists('protocolos');
    }
}
