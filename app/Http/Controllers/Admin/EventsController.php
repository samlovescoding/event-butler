<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Event::paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'length' => 'required|integer|min:1',
            'maximum_allowed' => 'required|integer|min:1',
            'booking_start' => 'required|date',
            'booking_end' => 'required|date',
            'timing_start' => 'required|date_format:H:i',
            'timing_end' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors());
        }

        $event = new Event($validator->validated());

        $event->user_id = auth()->user()->id;

        $event->booking_start = Carbon::parse($event->booking_start);
        $event->booking_end = Carbon::parse($event->booking_end);

        $event->save();

        return $this->success($event);
    }

    /**
     * Display the specified resource.
     *
     * @param  Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        $this->success($event);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'length' => 'integer|min:1',
            'maximum_allowed' => 'integer|min:1',
            'booking_start' => 'date',
            'booking_end' => 'date',
            'timing_start' => 'date_format:H:i',
            'timing_end' => 'date_format:H:i',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors());
        }

        $event->update($validator->validated());

        return $this->success($event, "Event was updated.");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return $this->success($event, "Event was deleted.");
    }
}
