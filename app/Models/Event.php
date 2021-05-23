<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'event_id');
    }

    public function getTimeSlots()
    {
        $timingStart = explode(":", $this->timing_start);
        $timingEnd = explode(":", $this->timing_end);
        $timingCurrent = $timingStart;

        $slots = [];

        while ($timingCurrent[0] <= $timingEnd[0]) {
            if ($timingCurrent[0] == $timingEnd[0]) {
                if ($timingCurrent[1] >= $timingEnd[1]) {
                    break;
                }
            }
            $slotName = implode(":", $timingCurrent);
            $slots[$slotName] = 0;

            // Normalize the timestamps
            $timingCurrent[1] = $timingCurrent[1] + $this->length;
            if ($timingCurrent[1] >= 60) {
                $timingCurrent[0]++;
                $timingCurrent[1] -= 60;
            }
            if ($timingCurrent[0] < 10) {
                $timingCurrent[0] = "0" . intval($timingCurrent[0]);
            }
            if ($timingCurrent[1] < 10) {
                $timingCurrent[1] = "0" . intval($timingCurrent[1]);
            }
        }

        return $slots;
    }

    public function getBookedSlotsAttribute()
    {
        return collect($this->getSlotsAttribute())->map(function ($day) {
            return collect($day)->filter(function ($slot) {
                return $slot != 0;
            });
        });
    }

    public function getSlotsAttribute()
    {
        $activeDays = explode(",", $this->active_days);
        $bookingStart = Carbon::parse($this->booking_start);
        $bookingEnd = Carbon::parse($this->booking_end);
        $bookingCurrent = $bookingStart;
        $emptyTimeSlots = $this->getTimeSlots();
        $days = [];
        while ($bookingCurrent <= $bookingEnd) {

            // Only the active days
            if (!in_array($bookingCurrent->dayOfWeekIso, $activeDays)) {
                $bookingCurrent->addDay();
                continue;
            }


            $days[$bookingCurrent->format("Y-m-d")] = $emptyTimeSlots;
            $bookingCurrent->addDay();
        }

        $appointments = $this->appointments()->select("slot_time")->get();
        foreach ($appointments as $appointment) {
            // For all appointments subtract 1 from the slots
            $appointmentTime = explode(" ", $appointment->slot_time);
            $days[$appointmentTime[0]][$appointmentTime[1]] += 1;
        }

        return $days;
    }

    public function isSlotValid($timeSlot)
    {
        $timeSlot = explode(" ", $timeSlot);
        $slots = $this->getSlotsAttribute();
        if (in_array($timeSlot[0], array_keys($slots))) {
            if (in_array($timeSlot[1], array_keys($slots[$timeSlot[0]]))) {

                return true;
            }
        }return false;
    }

    public function isSlotAvailable($timeSlot)
    {
        $appointmentCount = $this->appointments()->where("slot_time", $timeSlot)->count();

        if ($appointmentCount >= $this->maximum_allowed) {
            return false;
        }

        return true;
    }
}
