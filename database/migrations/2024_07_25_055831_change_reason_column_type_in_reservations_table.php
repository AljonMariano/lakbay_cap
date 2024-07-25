<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeReasonColumnTypeInReservationsTable extends Migration
{
    public function up()
    {
        // First, check if the reason column exists
        if (Schema::hasColumn('reservations', 'reason')) {
            // Update any NULL values to an empty string
            DB::statement("UPDATE reservations SET reason = '' WHERE reason IS NULL");
            
            // Then, modify the column
            Schema::table('reservations', function (Blueprint $table) {
                $table->text('reason')->nullable()->change();
            });
        } else {
            // If the column doesn't exist, create it
            Schema::table('reservations', function (Blueprint $table) {
                $table->text('reason')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
}