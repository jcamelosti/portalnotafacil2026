<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('empresas', function (Blueprint $table) {
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
