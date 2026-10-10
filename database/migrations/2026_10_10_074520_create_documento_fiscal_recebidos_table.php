<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentoFiscalRecebidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_fiscal_recebidos', function (Blueprint $table) {
            $table->id();

             $table->foreignId('empresa_id')
                ->constrained('empresas')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('nsu');

            $table->string('tipo_documento', 20);

            $table->longText('xml')->nullable();

            $table->json('dados')->nullable();

            $table->timestamps();

            // Impede NSU duplicado para a mesma empresa
            $table->unique(['empresa_id', 'nsu']);

            // Facilita consultas por tipo de documento
            $table->index(['empresa_id', 'tipo_documento']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documento_fiscal_recebidos');
    }
}
