<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorrelacaoTribMunTribNacTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correlacao_trib_mun_trib_nac', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('empresa_id')->index();
            $table->foreign('empresa_id')
                ->references('id')
                ->on('empresas');

            $table->string('cTribMun', 20);
            $table->string('xTribMun', 500);

            $table->decimal('aliquota', 8, 5)->nullable();

            $table->string('cTribNac', 20)->nullable();
            $table->string('xTribNac', 500)->nullable();

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
        Schema::dropIfExists('correlacao_trib_mun_trib_nac');
    }
}
