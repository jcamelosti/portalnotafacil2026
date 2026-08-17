<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresaAtividadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresa_atividades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            /*$table->foreign('empresa_id')
                ->references('id')
                ->on('empresas');*/
            $table->string('codigo_atividade');
            $table->string('descricao_atividade');
            $table->date('vigencia_inicial');
            $table->date('vigencia_final')->nullable();
            $table->decimal('aliquota',15,2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresa_atividades');
    }
}
