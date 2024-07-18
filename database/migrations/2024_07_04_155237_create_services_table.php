<?php
// database/migrations/xxxx_xx_xx_create_services_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('description')->nullable();
            $table->integer('time');
            $table->integer('amount');
            $table->integer('discount')->nullable();
            $table->string('images')->nullable();
            $table->foreignId('category_id')->constrained('service_categories')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
}
