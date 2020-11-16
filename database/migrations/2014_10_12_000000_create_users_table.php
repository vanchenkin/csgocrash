<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->string('steamid');
            $table->string('steamid64');
            $table->string('steamLink');
            $table->string('tradeLink')->nullable()->default(null);
            $table->double('money', 10, 2)->default('0');
            $table->string('refcode')->nullable()->default(null);
            $table->enum('role', ['admin', 'moderator', 'default', 'fake'])->default('default');
            $table->integer('chatBan')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
