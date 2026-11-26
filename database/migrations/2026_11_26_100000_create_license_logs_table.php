<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicenseLogsTable extends Migration
{
    /**
     * Histórico de alterações de licenças feitas pelo admin.
     * Sem FKs físicas para não depender do tipo das PKs de licenses/users/planos.
     */
    public function up()
    {
        Schema::create('license_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('license_id')->index();
            $table->unsignedBigInteger('empresa_id')->index();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('validate_anterior')->nullable();
            $table->date('validate_nova')->nullable();
            $table->unsignedBigInteger('plano_anterior_id')->nullable();
            $table->unsignedBigInteger('plano_novo_id')->nullable();
            $table->string('observacao', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('license_logs');
    }
}
