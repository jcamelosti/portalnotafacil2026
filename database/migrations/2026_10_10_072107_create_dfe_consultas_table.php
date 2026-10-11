<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDfeConsultasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dfe_consultas', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('empresa_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('ult_nsu')->default(0);
            $table->unsignedBigInteger('max_nsu')->default(0);
            $table->string('status')->default('pendente');
            $table->text('erro')->nullable();
            $table->timestamp('iniciada_em')->nullable();
            $table->timestamp('finalizada_em')->nullable();
            
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
        Schema::dropIfExists('dfe_consultas');
    }
}
