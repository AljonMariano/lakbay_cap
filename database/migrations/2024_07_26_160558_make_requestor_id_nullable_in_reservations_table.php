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
            // Remove the line that adds the destination_activity column
            // $table->string('destination_activity')->nullable()->after('rs_status');
            
            // If you want to make any changes to the existing destination_activity column, you can do it here
            // For example, if you want to make it nullable:
            // $table->string('destination_activity')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Remove any changes made in the up method
            // For example, if you made destination_activity nullable:
            // $table->string('destination_activity')->nullable(false)->change();
        });
    }
};