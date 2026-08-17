<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresaCnaesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresa_cnaes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
//            $table->foreign('empresa_id')
//                ->references('id')
//                ->on('empresas');

            $table->string('codigo_cnae');
            $table->string('descricao_cnae');
            $table->integer('principal');

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
        Schema::dropIfExists('empresa_cnaes');
    }
}
