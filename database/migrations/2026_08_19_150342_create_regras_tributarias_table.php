<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegrasTributariasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regras_tributarias', function (Blueprint $table) {
            $table->unsignedSmallInteger('ano')->primary();

            $table->decimal('p_ibs_uf', 8, 4)->default(0);
            $table->decimal('p_ibs_mun', 8, 4)->default(0);
            $table->decimal('p_cbs', 8, 4)->default(0);

            $table->date('vigencia_inicio')->nullable();
            $table->date('vigencia_fim')->nullable();

            $table->timestamps();

            $table->index(['vigencia_inicio', 'vigencia_fim'], 'vig_ini_fim');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('regras_tributarias');
    }
}
