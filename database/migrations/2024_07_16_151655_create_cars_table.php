<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand');
            $table->integer('year');
            $table->string('license_plate')->unique();
            $table->string('category');
            $table->text('description')->nullable();
            $table->boolean('available')->default(true);
            $table->decimal('base_price_per_day', 8, 2);
            $table->json('age_policy')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cars');
    }
}
