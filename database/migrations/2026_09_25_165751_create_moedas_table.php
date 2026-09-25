<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoedasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moedas', function (Blueprint $table) {
            $table->id();

            $table->string('codigo_numerico', 3)
                ->unique()
                ->comment('Código numérico da moeda conforme ISO 4217');

            $table->string('nome', 100);

            $table->string('simbolo', 10)
                ->nullable();

            $table->string('codigo_pais', 2)
                ->nullable()
                ->comment('Código do país conforme ISO 3166-1 Alpha-2');

            $table->string('pais', 100)
                ->nullable();

            $table->timestamps();

            $table->index('codigo_pais');
            $table->index('nome');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moedas');
    }
}
