<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            ['name' => 'Awaiting Allocation'],
            ['name' => 'Ready to Start'],
            ['name' => 'In Development'],
            ['name' => 'In Testing'],
            ['name' => 'In Review'],
            ['name' => 'Done'],
        ];

        foreach ($statuses as $status) {
            Status::create($status);
        }
    }
}
