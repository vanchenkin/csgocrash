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
            $table->string('username');
            $table->string('avatar');
            $table->string('steamid');
            $table->string('steamid64');
            $table->string('steamLink');
            $table->string('tradeLink')->nullable()->default(null);
            //$table->string('accessSalt')->default("");
            $table->string('money')->default('0');
            $table->string('refcode')->nullable()->default(null);
            $table->enum('role', ['admin', 'moderator', 'default', 'fake'])->default('default');
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
