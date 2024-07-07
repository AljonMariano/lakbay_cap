<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOffIdToReservationsTable extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('off_id')->nullable()->after('requestor_id');
            $table->foreign('off_id')->references('off_id')->on('offices')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['off_id']);
            $table->dropColumn('off_id');
        });
    }
}