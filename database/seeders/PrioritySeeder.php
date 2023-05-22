<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Priority;

class PrioritySeeder extends Seeder
{
    public function run()
    {
        $priorities = [
            ['name' => 'Highest'],
            ['name' => 'High'],
            ['name' => 'Medium'],
            ['name' => 'Low'],
            ['name' => 'Lowest'],
        ];

        Priority::insert($priorities);
    }
}
