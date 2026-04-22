<?php

namespace Database\Factories;

use App\Models\BreakTime;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class BreakTimeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BreakTime::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startTime = $this->faker->dateTimeBetween('12:00', '13:00');
        $endTime = $this->faker->dateTimeBetween($startTime->format('H:i'), '14:00');

        return [
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }
}