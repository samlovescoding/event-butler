<?php

namespace App\Models;

// use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class Event extends Model
{
  use HasFactory;
  protected $guarded = [];
  public function cacheKey()
  {
    return sprintf(
      "%s:%s:%s",
      $this->getTable(),
      $this->getKey(),
      $this->updated_at->timestamp
    );
  }
  public function appointments()
  {
    return $this->hasMany(Appointment::class, 'event_id');
  }

  public function getTimeSlotsOld()
  {
    $timingStart = explode(":", $this->timing_start);
    $timingEnd = explode(":", $this->timing_end);
    $timingCurrent = $timingStart;

    $inactiveStart = explode(":", $this->inactive_start);
    $inactiveEnd = explode(":", $this->inactive_end);

    $slots = [];

    while ($timingCurrent[0] <= $timingEnd[0]) {
      if ($timingCurrent[0] == $timingEnd[0]) {
        if ($timingCurrent[1] >= $timingEnd[1]) {
          break;
        }
      }
      $slotName = implode(":", $timingCurrent);
      if ($timingCurrent[0] >= $inactiveStart[0]) {
        if ($timingCurrent[1] >= $inactiveStart[1]) {
          if ($timingCurrent[0] <= $inactiveEnd[0]) {
            if ($slotName < $this->inactive_end) {
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
              continue;
            }
          }
        }
      }

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

  public function getTimeSlots()
  {
    $slots = [];
    $timingStart = Carbon::parse($this->timing_start);
    $timingEnd = Carbon::parse($this->timing_end);
    $timingCurrent = $timingStart;
    $inactiveStart = Carbon::parse($this->inactive_start);
    $inactiveEnd = Carbon::parse($this->inactive_end);

    while ($timingCurrent->lt($timingEnd)) {
      if ($timingCurrent->between($inactiveStart, $inactiveEnd, true)) {
        $timingCurrent->addMinutes($this->length);
        continue;
      }

      $slots[$timingCurrent->toTimeString()] = 0;
      $timingCurrent->addMinutes($this->length);
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

  public function getCachedBookedSlotsAttribute()
  {
    return Cache::remember($this->cacheKey() . ":slots", 60, function () {
      return $this->getBookedSlotsAttribute();
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
    }
    return false;
  }

  public function isSlotAvailable($timeSlot)
  {
    $appointmentCount = $this->appointments()->where("slot_time", $timeSlot)->count();

    if ($appointmentCount >= $this->maximum_allowed) {
      return false;
    }

    return true;
  }

  public function isSlotInPast($timeSlot)
  {
    $currentTime = Carbon::now();
    $timeSlot = Carbon::parse($timeSlot);
    if ($timeSlot->lte($currentTime)) {
      return false;
    }
    return true;
  }
}
