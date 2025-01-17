<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('car_images', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('car_id')->unsigned();
      $table->string('image_name');
      $table->boolean('removed')->default(0);
      $table->timestamps();

      $table->foreign('car_id')->references('id')->on('cars')
        ->onDelete('cascade')->onUpdate('cascade');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('car_images');
  }
};
