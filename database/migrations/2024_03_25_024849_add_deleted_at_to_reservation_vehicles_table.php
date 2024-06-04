<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->integer('vh_capacity')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('reservation_vehicles', function (Blueprint $table) {
            $table->dropsoftDeletes();
        });

        Schema::dropIfExists('vehicles');
    }
};
