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
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('rs_from')->nullable()->after('rs_passengers');
            $table->date('rs_date_start')->nullable()->after('rs_from');
            $table->time('rs_time_start')->nullable()->after('rs_date_start');
            $table->date('rs_date_end')->nullable()->after('rs_time_start');
            $table->time('rs_time_end')->nullable()->after('rs_date_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['rs_from', 'rs_date_start', 'rs_time_start', 'rs_date_end', 'rs_time_end']);
        });
    }
};