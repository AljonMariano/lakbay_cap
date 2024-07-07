<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyForeignKeyOnReservationVehiclesTable extends Migration
{
    public function up()
    {
        Schema::table('reservation_vehicles', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['reservation_id']);
            
            // Add the new foreign key constraint with cascading delete
            $table->foreign('reservation_id')
                  ->references('reservation_id')
                  ->on('reservations')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('reservation_vehicles', function (Blueprint $table) {
            // Drop the cascading delete foreign key constraint
            $table->dropForeign(['reservation_id']);
            
            // Revert to the original foreign key constraint
            $table->foreign('reservation_id')
                  ->references('reservation_id')
                  ->on('reservations')
                  ->onDelete('restrict');
        });
    }
}