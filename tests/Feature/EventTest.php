<?php

namespace Tests\Feature;

use App\Models\Event;
use Tests\TestCase;

class EventTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_event_count()
    {
        $response = $this->get('/api/events');

        $eventCount = Event::count();

        $response->assertStatus(200)->assertJsonCount($eventCount + 3, 'data.*');

    }
}
