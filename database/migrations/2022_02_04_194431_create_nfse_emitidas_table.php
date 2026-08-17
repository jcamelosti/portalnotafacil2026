<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNfseEmitidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nfse_emitidas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tomador_id')->nullable();
            $table->string('nfse_id', 255)->nullable();
            $table->string('integracao_id', 255)->nullable();
            $table->string('data_emissao_br', 255)->nullable();
            $table->string('situacao', 255)->nullable();
            $table->string('numero_nfse', 255)->nullable();
            $table->string('numero_rps', 255)->nullable();
            $table->string('codigo_verificacao', 255)->nullable();
            $table->text('mensagem')->nullable();
            $table->string('url_pdf',255)->nullable();
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
        Schema::dropIfExists('nfse_emitidas');
    }
}
