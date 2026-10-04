<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndOpIbsCbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ind_op_ibs_cbs', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)
                ->unique()
                ->comment('Código indOp');

            $table->string('descricao', 255)
                ->comment('Descrição da operação');

            $table->string('local_operacao', 255)
                ->nullable()
                ->comment('Local da operação');

            $table->string('local_fornecimento', 255)
                ->nullable()
                ->comment('Local do fornecimento');

            $table->boolean('ativo')
                ->default(true);

            $table->timestamps();

            $table->index('ativo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ind_op_ibs_cbs');
    }
}
