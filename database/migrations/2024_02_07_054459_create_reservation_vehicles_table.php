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
        Schema::create('reservation_vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id');
            $table->unsignedBigInteger('driver_id')->nullable();;
            $table->unsignedBigInteger('vehicle_id');
            $table->foreign('reservation_id')->references('reservation_id')->on('reservations');
            $table->foreign('driver_id')->references('driver_id')->on('drivers');
            $table->foreign('vehicle_id')->references('vehicle_id')->on('vehicles');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_vehicles');
    }
};
