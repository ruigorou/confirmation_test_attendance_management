<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attendance::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = $this->faker->dateTimeBetween('-3 months', 'now');
        $clockIn = $this->faker->dateTimeBetween($date->format('Y-m-d') . ' 08:00', $date->format('Y-m-d') . ' 10:00');
        $clockOut = $this->faker->dateTimeBetween($clockIn->format('Y-m-d H:i'), $date->format('Y-m-d') . ' 18:00');

        return [
            'date' => $date->format('Y-m-d'),
            'clock_in' => $clockIn->format('H:i:s'),
            'clock_out' => $clockOut->format('H:i:s'),
            'status' => $this->faker->randomElement(['勤務外', '出勤中', '休憩中', '退勤済', '休暇']),
        ];
    }
}