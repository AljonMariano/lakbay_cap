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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id('driver_id');
            $table->integer('dr_emp_id');
            $table->string('dr_fname');
            $table->string('dr_mname');
            $table->string('dr_lname');
            $table->unsignedBigInteger('off_id');
            $table->foreign('off_id')->references('off_id')->on('offices');
            $table->string('dr_status');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
