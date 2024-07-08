<?php
// database/migrations/xxxx_xx_xx_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name');
            $table->string('surname')->nullable();
            $table->string('password');
            $table->string('telephone')->unique()->nullable();
            $table->string('address')->nullable();
            $table->string('logo')->nullable();
            $table->string('description')->nullable();
            $table->string('otp')->nullable();
            $table->string('reset_password_otp')->nullable();
            $table->dateTime('reset_password_expires')->nullable();
            $table->enum('role', ['ADMIN', 'PROVIDER', 'CLIENT'])->default('CLIENT');
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
