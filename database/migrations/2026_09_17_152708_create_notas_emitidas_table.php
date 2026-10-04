<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotasEmitidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notas_emitidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')
                ->constrained('empresas')
                ->cascadeOnDelete();

            $table->foreignId('tomador_id')
                ->nullable()
                ->constrained('tomadores')
                ->nullOnDelete();

            $table->longText('nfse_xml')
                ->nullable();

            $table->string('num_nfse', 30)
                ->nullable();

            $table->decimal('valor', 15, 2)
                ->default(0);

            $table->json('dados_emissao')->nullable();

            $table->timestamps();

            //$table->index('num_nfse');
            $table->index(['empresa_id', 'num_nfse']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notas_emitidas');
    }
}
