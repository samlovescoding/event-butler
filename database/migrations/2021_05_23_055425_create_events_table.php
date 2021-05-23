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

            // Booking Days
            $table->date("booking_start");
            $table->date("booking_end");

            // Booking Day Timings
            $table->time("timing_start");
            $table->time("timing_end");

            $table->string("active_days")->default('1,2,3,4,5'); // Event can be booked only for these days

            $table->time("inactive_start");
            $table->time("inactive_end");

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
