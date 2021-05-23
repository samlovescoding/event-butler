<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $month = rand(6, 10);
        return [
            "name" => $this->faker->name() . " Event",
            "length" => $this->faker->randomElement([10, 20, 30, 60]),
            "maximum_allowed" => $this->faker->randomElement([2, 5, 10, 20]),
            "booking_start" => "2021-" . $month . "-" . rand(10, 20),
            "booking_end" => "2021-" . $month . "-" . rand(20, 30),
            "timing_start" => $this->getRandomStartHour() . ':' . $this->getRandomMinute(),
            "timing_end" => $this->getRandomEndHour() . ':' . $this->getRandomMinute(),
            "user_id" => rand(1, 10),
        ];
    }

    public function getRandomStartHour()
    {
        return rand(6, 12);
    }
    public function getRandomEndHour()
    {
        return rand(13, 18);
    }
    public function getRandomMinute()
    {
        return $this->faker->randomElement([15, 30, 45]);
    }
}
