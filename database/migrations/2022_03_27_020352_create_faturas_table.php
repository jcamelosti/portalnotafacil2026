<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::create('faturas', function (Blueprint $table) {
            $table->id();
            $table->string('num_doc');
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->unsignedBigInteger('empresa_id')->index()->nullable();
            $table->foreign('empresa_id')
                ->references('id')
                ->on('empresas');

            $table->unsignedBigInteger('fatura_status_id')->index()->nullable();
            $table->foreign('fatura_status_id')
                ->references('id')
                ->on('fatura_status');

            $table->decimal('total', 15,2);
            $table->text('transacao_id');
            $table->text('safe2pay_pix_data')->nullable();
            $table->timestamps();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faturas');
    }
}
