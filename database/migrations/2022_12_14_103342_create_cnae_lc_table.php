<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCnaeLcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cnae_lc', function (Blueprint $table) {
            $table->id();
            $table->string('cnae_mascara', 255);
            $table->string('cnae', 255);
            $table->text('descricao_cnae');
            $table->string('item_lc');
            $table->text('descricao_item');
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
        Schema::dropIfExists('cnae_lc');
    }
}
