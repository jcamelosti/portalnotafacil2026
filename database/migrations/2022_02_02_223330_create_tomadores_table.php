<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTomadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tomadores', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')
                ->references('id')
                ->on('empresas');

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

            $table->enum('tipo_logradouro', [
                "Alameda",
                "Avenida",
                "Chácara",
                "Colônia",
                "Condomínio",
                "Estância",
                "Estrada",
                "Fazenda",
                "Praça",
                "Prolongamento",
                "Rodovia",
                "Rua",
                "Sítio",
                "Travessa",
                "Vicinal",
                "Eqnp"
           ])->default("Rua");
           $table->enum('tipo_bairro', [
            'Bairro',
            'Bosque',
            'Chácara',
            'Conjunto',
            'Desmembramento',
            'Distrito',
            'Favela',
            'Fazenda',
            'Gleba',
            'Horto',
            'Jardim',
            'Loteamento',
            'Núcleo',
            'Parque',
            'Residencial',
            'Sítio',
            'Tropical',
            'Vila',
            'Zona',
            'Centro',
            'Setor',
           ])->default("Bairro");
            
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
        Schema::dropIfExists('tomadores');
    }
}
