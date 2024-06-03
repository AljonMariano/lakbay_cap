<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id('vehicle_id');
            $table->string('vh_plate');
            $table->string('vh_type');
            $table->string('vh_brand')->nullable();
            $table->integer('vh_year');
            $table->integer('vh_capacity');
            $table->string('vh_fuel_type')->nullable();
            $table->string('vh_condition');
            $table->string('vh_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
