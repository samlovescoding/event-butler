<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Event $event)
    {
        $appointments = $event->appointments()->get()->append(["date", "time"]);

        return $this->success($appointments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Event $event, Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'email' => 'required|email',
                'first_name' => 'required',
                'last_name' => 'required',
                'slot_time' => 'required|date',
            ]
        );

        if ($validator->fails()) {
            return $this->fail($validator->errors());
        }

        $validated = $validator->validated();

        if (!$event->isSlotValid($validated["slot_time"])) {
            return $this->fail("Time Slot is invalid.");
        }

        if (!$event->isSlotAvailable($validated["slot_time"])) {
            return $this->fail("Time Slot is unavailable because it is fully booked.");
        }

        $appointment = new Appointment($validated);

        $appointment->event_id = $event->id;

        $appointment->save();

        return $this->success($appointment, "Appointment was booked.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Appointment $appointment)
    {
        return $appointment;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return $this->success($appointment, "Appointment was cancelled.");
    }
}
