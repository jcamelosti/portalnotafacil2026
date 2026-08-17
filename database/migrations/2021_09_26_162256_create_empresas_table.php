<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->string('razao_social');
            $table->string('nome_fantasia')->nullable();
            $table->string('email');
            $table->string('cpf_cnpj');
            $table->string('inscricao_municipal');
            $table->string('telefone1')->nullable();
            $table->string('telefone2')->nullable();
            $table->string('cep');
            $table->string('logradouro');
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro');
            $table->string('cidade_id');
            $table->integer('is_mei')->default(1);
            $table->integer('is_optante_simples_nac')->default(1);
            $table->date('vig_ini_simples_nac');
            $table->date('vig_fim_simples_nac');
            $table->integer('permite_deducao');
            $table->integer('permite_desc_incond');
            $table->integer('permite_desc_cond');
            $table->integer('ambiente_transmissao')->default(1);
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
        Schema::dropIfExists('empresas');
    }
}
