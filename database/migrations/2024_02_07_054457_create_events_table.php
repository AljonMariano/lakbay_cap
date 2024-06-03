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
        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id');
            $table->string('ev_name', 100);
            $table->string('ev_venue', 100);
            $table->date('ev_date_start')->nullable();
            $table->time('ev_time_start')->nullable();
            $table->date('ev_date_end')->nullable();
            $table->time('ev_time_end')->nullable();
            $table->date('ev_date_added')->nullable();
            $table->timestamps(); // This will add `created_at` and `updated_at` columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
