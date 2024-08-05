<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOutsiderColumnsToReservationsTable extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->boolean('is_outsider')->default(false);
            $table->string('outside_office')->nullable();
            $table->string('outside_requestor')->nullable();
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['is_outsider', 'outside_office', 'outside_requestor']);
        });
    }
}