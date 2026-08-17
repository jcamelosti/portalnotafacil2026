<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->date('dt_nasc')->nullable();
            $table->string('cpf_cnpj', 18)->unique();
            $table->string('cep', 9);//75080-120
            $table->string('numero', 255)->nullable();
            $table->string('endereco', 255);
            $table->text('complemento');
            $table->string('bairro', 255);
            $table->integer('cidade_id');

            $table->string('telefone1', 17);
            $table->string('telefone2', 17)
                ->nullable()
                ->default(null);

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
        Schema::dropIfExists('clientes');
    }
}
