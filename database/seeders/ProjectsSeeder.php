<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProjectsSeeder extends Seeder
{
    /**
     * Run the seeder to populate the projects table.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 5; $i++) {
            $faker = Faker::create();

            Project::create([
                'name' => $faker->sentence(3),
                'description' => $faker->paragraph(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
