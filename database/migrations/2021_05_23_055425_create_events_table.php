<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string("name");

            // Appointment length in minutes
            $table->integer("length");

            // Maximum appointments per slot
            $table->integer("maximum_allowed");

            // Booking start date
            $table->time("booking_start");
            $table->time("booking_end");

            // Booking Day Timings
            $table->time("timing_start");
            $table->time("timing_end");

            // User ID
            $table->bigInteger("user_id");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
