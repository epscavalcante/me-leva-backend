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
        Schema::create('rides', function (Blueprint $table) {
            $table->uuid('ride_id')->primary();
            $table->uuid('passenger_id');
            $table->uuid('driver_id')->nullable();
            $table->string('status');
            $table->string('from_latitude');
            $table->string('from_longitude');
            $table->string('to_latitude');
            $table->string('to_longitude');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
