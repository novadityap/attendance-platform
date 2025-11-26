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
    Schema::create('departments', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->string('name', 255);
      $table->time('max_check_in_time');
      $table->time('max_check_out_time');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('departments');
  }
};
