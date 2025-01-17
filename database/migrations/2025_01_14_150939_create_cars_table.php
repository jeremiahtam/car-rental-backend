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
    Schema::create('cars', function (Blueprint $table) {
      $table->increments('id');
      $table->string('brand');
      $table->string('model');
      $table->string('slug');

      $table->boolean('aircondition')->default(0);
      $table->string('gear_type');
      $table->string('fuel_type');
      $table->integer('seats');
      $table->integer('airbags');

      $table->double('cost_per_meter', 8, 2)->default(0.00);
      $table->double('wait_amount_per_hour', 8, 2)->default(0.00);
      $table->boolean('removed')->default(0);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('cars');
  }
};
