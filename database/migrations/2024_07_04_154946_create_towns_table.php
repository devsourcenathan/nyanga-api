<?php
// database/migrations/xxxx_xx_xx_create_towns_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTownsTable extends Migration
{
    public function up()
    {
        Schema::create('towns', function (Blueprint $table) {
            $table->id();
            $table->string('label')->unique();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('towns');
    }
}
