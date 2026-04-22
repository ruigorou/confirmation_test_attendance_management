<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminUserSeeder::class,
        ]);

        \App\Models\User::factory(10)->create();

        // Create attendances for users
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $attendances = \App\Models\Attendance::factory(rand(5, 20))->create([
                'user_id' => $user->id,
            ]);

            // Create break times for each attendance
            foreach ($attendances as $attendance) {
                \App\Models\BreakTime::factory(rand(0, 2))->create([
                    'attendance_id' => $attendance->id,
                ]);
            }
        }
    }
}
