<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateReservationsWithOfficeId extends Migration
{
    public function up()
    {
        // First, check if the off_id column exists in the requestors table
        if (!Schema::hasColumn('requestors', 'off_id')) {
            // If it doesn't exist, add the column
            Schema::table('requestors', function ($table) {
                $table->unsignedBigInteger('off_id')->nullable();
            });
        }

        // Now update the reservations table
        DB::table('reservations')
            ->join('requestors', 'reservations.requestor_id', '=', 'requestors.requestor_id')
            ->update(['reservations.off_id' => DB::raw('requestors.off_id')]);
    }

    public function down()
    {
        // Reverse the changes
        DB::table('reservations')->update(['off_id' => null]);
        
        // Remove the off_id column from requestors if it was added
        if (Schema::hasColumn('requestors', 'off_id')) {
            Schema::table('requestors', function ($table) {
                $table->dropColumn('off_id');
            });
        }
    }
}