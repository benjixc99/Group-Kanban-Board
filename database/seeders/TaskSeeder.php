<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Status;
use App\Models\User;
use Faker\Factory as Faker;

class TaskSeeder extends Seeder
{
    public function run()
    {
        // Create tasks for each project
        $projects = Project::all();
        $statuses = Status::all();
        $priorities = Priority::all();
        $users = User::nonAdmin()->get();

        foreach ($projects as $project) {
            $tasksCount = rand(5, 10);

            for ($i = 1; $i <= $tasksCount; $i++) {
                $estimate = rand(1, 10) * 8; // Assuming estimate is in hours
                $faker = Faker::create();

                $project->tasks()->create([
                    'name' => $faker->sentence(3),
                    'description' => $faker->paragraph(),
                    'priority_id' => $priorities->random()->id,
                    'status_id' => $statuses->random()->id,
                    'estimate' => $estimate,
                    'progress' => $estimate - rand(1, $estimate),
                    'assignee_id' => $users->random()->id,
                ]);
            }
        }
    }
}
