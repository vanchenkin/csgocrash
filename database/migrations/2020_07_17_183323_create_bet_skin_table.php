<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBetSkinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bet_skin', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bet_id')->unsigned();
            $table->foreign('bet_id')->references('id')->on('bets')->onDelete('cascade');
            $table->bigInteger('r_skin_id')->unsigned();
            $table->foreign('r_skin_id')->references('id')->on('rskins')->onDelete('cascade');
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
        Schema::dropIfExists('bet_skin');
    }
}
