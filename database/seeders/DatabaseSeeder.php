<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->run(UserSeeder::class);
        $this->run(ProjectsTableSeeder::class);
        $this->call(ProjectUserSeeder::class);
        $this->call(TaskSeeder::class);
    }
}
