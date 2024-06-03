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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('reservation_id');
            $table->bigInteger('rs_voucher')->nullable();
            $table->bigInteger('rs_passengers');
            $table->string('rs_travel_type', 255)->nullable();
            $table->string('rs_approval_status', 20)->nullable();
            $table->string('rs_status', 20)->nullable();
            $table->boolean('rs_cancelled')->default(false);
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('requestor_id');
            $table->foreign('event_id')->references('event_id')->on('events');
            $table->foreign('requestor_id')->references('requestor_id')->on('requestors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
