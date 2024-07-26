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
            $table->dropForeign(['event_id']); // Drop the foreign key constraint
            $table->dropColumn('event_id'); // Remove the event_id column
            $table->string('destination_activity')->after('rs_purpose'); // Add the new column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('destination_activity');
            $table->unsignedBigInteger('event_id')->nullable();
            $table->foreign('event_id')->references('event_id')->on('events');
        });
    }
};